<?php

namespace App\Services\Admin;

use App\DTOs\ProductDTO;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Tag;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;

class ProductBulkUpdateService
{
    public const CLEAR = '[CLEAR]';

    /** The first row is intentionally stable: admins should not rename these keys. */
    private const COLUMNS = [
        'SKU' => 'Product identifier. Do not change this value; it is used to find the existing product.',
        'Product Name' => 'Product name shown to customers. Leave blank to keep the current name.',
        'Slug' => 'URL slug, for example flower-garland. Leave blank to keep the current slug.',
        'Short Description' => 'Short product summary. Leave blank to keep it; type [CLEAR] to remove it.',
        'Description' => 'Full product description. Leave blank to keep it; type [CLEAR] to remove it.',
        'Video URL' => 'Product video URL. Leave blank to keep it; type [CLEAR] to remove it.',
        'Size/Weight Content' => 'Size and weight information text. Leave blank to keep it; type [CLEAR] to remove it.',
        'Size Details' => 'Additional size details text. Leave blank to keep it; type [CLEAR] to remove it.',
        'Perfect Placement' => 'Where the product is best used/displayed. Leave blank to keep it; type [CLEAR] to remove it.',
        'Price' => 'Regular price in INR. Enter a number, for example 599.',
        'Sale Price' => 'Selling price in INR. Enter a number lower than Price; [CLEAR] removes the sale price.',
        'Stock' => 'Available stock as a whole number, for example 25.',
        'Weight (Kg)' => 'Shipping weight in kilograms, for example 0.500.',
        'Length (Cm)' => 'Package length in centimetres.',
        'Width (Cm)' => 'Package width in centimetres.',
        'Height (Cm)' => 'Package height in centimetres.',
        'HSN Code' => 'Tax HSN code. Leave blank to keep it; type [CLEAR] to remove it.',
        'Status' => 'Use Active or Inactive to control store visibility.',
        'Featured' => 'Use Yes or No to control featured placement.',
        'Trending' => 'Use Yes or No to control trending placement.',
        'Category Slug' => 'Existing category slug. Leave blank to keep it; type [CLEAR] to remove the category.',
        'Collections' => 'Comma-separated existing collection slugs or names. Blank keeps current; [CLEAR] removes all.',
        'Tags' => 'Comma-separated existing tag slugs or names. Blank keeps current; [CLEAR] removes all.',
        'Variations' => 'Use: Color=Red; Size=Large; Items=1; SKUs=PB1 || Color=Blue; Size=Medium; Items=2; SKUs=PB2. Blank keeps current; [CLEAR] removes all.',
        'Meta Title' => 'SEO meta title. Leave blank to keep it; type [CLEAR] to remove it.',
        'Meta Description' => 'SEO meta description. Leave blank to keep it; type [CLEAR] to remove it.',
        'Meta Keywords' => 'Comma-separated SEO keywords. Leave blank to keep them; type [CLEAR] to remove them.',
        'Canonical URL' => 'SEO canonical URL. Leave blank to keep it; type [CLEAR] to remove it.',
        'Meta Robots' => 'Use index,follow, noindex,follow, or noindex,nofollow. Blank keeps current.',
    ];

    public function __construct(private ProductService $productService) {}

    public function headers(): array
    {
        return array_keys(self::COLUMNS);
    }

    public function export(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');
        $headers = $this->headers();

        foreach ($headers as $index => $header) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $cell = $sheet->getCell($column . '1');
            $cell->setValue($header);
            $sheet->getComment($column . '1')->getText()->createTextRun(self::COLUMNS[$header]);
            $sheet->getColumnDimension($column)->setWidth(max(16, min(42, strlen($header) + 8)));
        }
        $sheet->getStyle('A1:' . Coordinate::stringFromColumnIndex(count($headers)) . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(42);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:' . Coordinate::stringFromColumnIndex(count($headers)) . '1');

        Product::with(['collections', 'tags', 'variants', 'seoMeta', 'category'])
            ->orderBy('id')
            ->chunk(200, function ($products) use ($sheet, $headers): void {
                foreach ($products as $product) {
                    $row = $this->productRow($product);
                    $sheet->fromArray(array_map(fn ($header) => $row[$header] ?? '', $headers), null, 'A' . ($sheet->getHighestRow() + 1));
                }
            });

        $instructions = $spreadsheet->createSheet();
        $instructions->setTitle('Instructions');
        $instructions->fromArray([
            ['KraftX Product Bulk Update'],
            ['1', 'Edit only existing product rows. SKU is the matching key and must not be changed.'],
            ['2', 'Do not add new rows for new products. Rows with unknown SKUs are rejected.'],
            ['3', 'Images are not included and will never be changed by this upload.'],
            ['4', 'Blank cells keep the existing value. Use [CLEAR] only when you intentionally want to remove a nullable value.'],
            ['5', 'Collections and tags accept comma-separated existing names or slugs.'],
            ['6', 'Variations use: Color=Red; Size=Large; Items=1; SKUs=PB1 || Color=Blue; Size=Medium; Items=2; SKUs=PB2'],
            ['7', 'Variation SKUs must belong to other existing products. The reciprocal link is maintained automatically.'],
            ['8', 'Save the edited file as .xlsx, then upload it from the Products admin page.'],
            ['', 'Each header in the Products sheet also has a detailed Excel comment.'],
        ]);
        $instructions->mergeCells('A1:B1');
        $instructions->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
        ]);
        $instructions->getColumnDimension('A')->setWidth(10);
        $instructions->getColumnDimension('B')->setWidth(115);
        $instructions->getStyle('A1:B10')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        $instructions->getRowDimension(1)->setRowHeight(30);

        return $spreadsheet;
    }

    public function import(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('Products') ?: $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        if (count($rows) < 2) {
            throw new RuntimeException('The workbook does not contain any product rows.');
        }

        $headerRow = array_map(fn ($value) => trim((string) $value), $rows[1]);
        $expected = $this->headers();
        $missing = array_values(array_diff($expected, $headerRow));
        if ($missing !== []) {
            throw new RuntimeException('This workbook is missing required headers: ' . implode(', ', $missing));
        }
        $positions = [];
        foreach ($expected as $header) {
            $positions[$header] = array_search($header, array_values($headerRow), true);
        }
        $result = ['updated' => 0, 'skipped' => 0, 'errors' => []];
        $seen = [];

        foreach (array_slice($rows, 1, null, true) as $rowNumber => $row) {
            $sku = trim((string) ($row[$this->columnLetter($positions['SKU'])] ?? ''));
            if ($sku === '') {
                continue;
            }
            if (isset($seen[$sku])) {
                $result['errors'][] = "Row {$rowNumber}: SKU '{$sku}' is duplicated in the file.";
                continue;
            }
            $seen[$sku] = true;
            $product = Product::with(['collections', 'tags', 'variants', 'seoMeta'])->where('sku', $sku)->first();
            if (! $product) {
                $result['errors'][] = "Row {$rowNumber}: SKU '{$sku}' does not match an existing product.";
                continue;
            }

            try {
                $this->updateProduct($product, $row, $positions, $rowNumber);
                $result['updated']++;
            } catch (\Throwable $e) {
                report($e);
                $result['errors'][] = "Row {$rowNumber} ({$sku}): {$e->getMessage()}";
            }
        }

        $result['skipped'] = count($seen) - $result['updated'] - count(array_filter($result['errors'], fn ($error) => str_contains($error, 'does not match')));
        return $result;
    }

    private function updateProduct(Product $product, array $row, array $positions, int $rowNumber): void
    {
        $value = fn (string $header) => trim((string) ($row[$this->columnLetter($positions[$header])] ?? ''));
        $data = $product->only(['name', 'slug', 'short_description', 'description', 'video_url', 'perfect_placement', 'price', 'weight', 'length', 'width', 'height', 'sale_price', 'stock', 'sku', 'hsn_code', 'status', 'featured', 'is_trending']);
        $nullable = ['short_description', 'description', 'video_url', 'perfect_placement', 'hsn_code'];
        foreach ($nullable as $field) {
            $input = $value($this->label($field));
            if ($input !== '') $data[$field] = $input === self::CLEAR ? null : $input;
        }
        foreach (['Size/Weight Content' => 'size_weight_content', 'Size Details' => 'size_details'] as $header => $field) {
            $input = $value($header);
            if ($input !== '') $data[$field] = $input === self::CLEAR ? null : $input;
        }
        foreach (['name', 'slug'] as $field) {
            $input = $value($this->label($field));
            if ($input !== '') $data[$field] = $input;
        }
        foreach (['price', 'weight', 'length', 'width', 'height', 'stock'] as $field) {
            $input = $value($this->label($field));
            if ($input !== '') {
                if (!is_numeric($input)) throw new RuntimeException("{$this->label($field)} must be numeric.");
                $data[$field] = in_array($field, ['stock'], true) ? (int) $input : (float) $input;
            }
        }
        $sale = $value('Sale Price');
        if ($sale !== '') {
            $data['sale_price'] = $sale === self::CLEAR ? null : (is_numeric($sale) ? (float) $sale : throw new RuntimeException('Sale Price must be numeric or [CLEAR].'));
        }
        if ($data['sale_price'] !== null && $data['sale_price'] >= $data['price']) throw new RuntimeException('Sale Price must be lower than Price.');
        foreach (['Status' => 'status', 'Featured' => 'featured', 'Trending' => 'is_trending'] as $header => $field) {
            $input = $value($header);
            if ($input !== '') $data[$field] = $this->boolean($input, $header);
        }

        $category = $value('Category Slug');
        if ($category !== '') {
            $data['category_id'] = $category === self::CLEAR ? null : (Category::where('slug', $category)->orWhere('name', $category)->value('id') ?: throw new RuntimeException("Category '{$category}' was not found."));
        }
        $collectionInput = $value('Collections');
        $tagInput = $value('Tags');
        $collectionIds = $collectionInput === '' ? $product->collections->pluck('id')->all() : $this->relationIds($collectionInput, Collection::class);
        $tagIds = $tagInput === '' ? $product->tags->pluck('id')->all() : $this->relationIds($tagInput, Tag::class);
        $variants = $this->variants($value('Variations'), $product);
        $seo = $this->seo($value, $product);

        $dto = ProductDTO::fromRequest($data, null, null, []);
        DB::transaction(function () use ($product, $data, $dto, $collectionIds, $tagIds, $variants, $seo): void {
            $this->productService->updateProduct($product->id, new ProductDTO(
                ...array_merge((array) $dto, ['collection_ids' => $collectionIds, 'tag_ids' => $tagIds, 'variants' => $variants, 'seo_meta' => $seo])
            ));
            $product->refresh();
            if (array_key_exists('category_id', $data) || array_key_exists('size_weight_content', $data) || array_key_exists('size_details', $data)) {
                $product->update(array_intersect_key($data, array_flip(['category_id', 'size_weight_content', 'size_details'])));
            }
        });
    }

    private function productRow(Product $product): array
    {
        return [
            'SKU' => $product->sku, 'Product Name' => $product->name, 'Slug' => $product->slug,
            'Short Description' => $product->short_description, 'Description' => $product->description, 'Video URL' => $product->video_url,
            'Size/Weight Content' => $product->size_weight_content, 'Size Details' => $product->size_details, 'Perfect Placement' => $product->perfect_placement,
            'Price' => $product->price, 'Sale Price' => $product->sale_price, 'Stock' => $product->stock, 'Weight (Kg)' => $product->weight,
            'Length (Cm)' => $product->length, 'Width (Cm)' => $product->width, 'Height (Cm)' => $product->height, 'HSN Code' => $product->hsn_code,
            'Status' => $product->status ? 'Active' : 'Inactive', 'Featured' => $product->featured ? 'Yes' : 'No', 'Trending' => $product->is_trending ? 'Yes' : 'No',
            'Category Slug' => $product->category?->slug, 'Collections' => $product->collections->pluck('slug')->implode(', '), 'Tags' => $product->tags->pluck('slug')->implode(', '),
            'Variations' => $product->variants->map(fn ($variant) => "Color={$variant->color}; Size={$variant->size}; Items={$variant->items_count}; SKUs=" . implode(',', (array) $variant->linked_skus))->implode(' || '),
            'Meta Title' => $product->seoMeta?->meta_title, 'Meta Description' => $product->seoMeta?->meta_description, 'Meta Keywords' => $product->seoMeta?->meta_keywords,
            'Canonical URL' => $product->seoMeta?->canonical_url, 'Meta Robots' => $product->seoMeta?->meta_robots,
        ];
    }

    private function relationIds(string $input, string $model): array
    {
        if ($input === '') return $model === Collection::class ? [] : [];
        if ($input === self::CLEAR) return [];
        $names = array_values(array_filter(array_map('trim', explode(',', $input))));
        $items = $model::where(function ($query) use ($names) { $query->whereIn('slug', $names)->orWhereIn('name', $names); })->get();
        $missing = array_diff(array_map('strtolower', $names), $items->flatMap(fn ($item) => [strtolower($item->slug), strtolower($item->name)])->all());
        if ($missing !== []) throw new RuntimeException('Unknown ' . class_basename($model) . ': ' . implode(', ', $missing));
        return $items->pluck('id')->all();
    }

    private function variants(string $input, Product $product): array
    {
        if ($input === '') return $product->variants->map(fn ($v) => $v->only(['id', 'size', 'color', 'items_count', 'linked_skus']))->all();
        if ($input === self::CLEAR) return [];
        return collect(explode('||', $input))->map(function ($part) use ($product) {
            $fields = [];
            foreach (explode(';', trim($part)) as $pair) { if (str_contains($pair, '=')) { [$key, $val] = array_map('trim', explode('=', $pair, 2)); $fields[strtolower($key)] = $val; } }
            $skus = array_values(array_filter(array_map('trim', explode(',', $fields['skus'] ?? ''))));
            if (($fields['color'] ?? '') === '' && ($fields['size'] ?? '') === '') throw new RuntimeException('Every variation needs Color or Size.');
            if (empty($skus)) throw new RuntimeException('Every variation needs at least one linked SKU.');
            if (in_array($product->sku, $skus, true)) throw new RuntimeException('A product cannot link to its own SKU.');
            $existing = Product::whereIn('sku', $skus)->pluck('sku')->all();
            if (count($existing) !== count(array_unique($skus))) throw new RuntimeException('One or more linked variation SKUs do not exist.');
            $items = (int) ($fields['items'] ?? 1);
            if ($items < 1) throw new RuntimeException('Variation Items must be at least 1.');
            return ['size' => $fields['size'] ?? null, 'color' => $fields['color'] ?? null, 'items_count' => $items, 'linked_skus' => array_values(array_unique($skus))];
        })->all();
    }

    private function seo(\Closure $value, Product $product): array
    {
        $seo = $product->seoMeta?->toArray() ?? [];
        foreach (['Meta Title' => 'meta_title', 'Meta Description' => 'meta_description', 'Meta Keywords' => 'meta_keywords', 'Canonical URL' => 'canonical_url', 'Meta Robots' => 'meta_robots'] as $header => $field) {
            $input = $value($header); if ($input !== '') $seo[$field] = $input === self::CLEAR ? null : $input;
        }
        return $seo;
    }

    private function boolean(string $input, string $field): bool
    {
        return match (strtolower($input)) { 'yes', 'y', '1', 'true', 'active' => true, 'no', 'n', '0', 'false', 'inactive' => false, default => throw new RuntimeException("{$field} must be Yes or No.") };
    }

    private function label(string $field): string
    {
        return ['short_description' => 'Short Description', 'description' => 'Description', 'video_url' => 'Video URL', 'perfect_placement' => 'Perfect Placement', 'hsn_code' => 'HSN Code'][$field] ?? Str::headline($field);
    }

    private function columnLetter(int $position): string
    {
        return Coordinate::stringFromColumnIndex($position + 1);
    }

    public function download(Spreadsheet $spreadsheet): void
    {
        (new Xlsx($spreadsheet))->save('php://output');
    }
}
