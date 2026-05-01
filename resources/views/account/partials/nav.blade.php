@php
    $links = [
        ['route' => 'account', 'label' => 'Dashboard', 'icon' => 'icon-HouseLine'],
        ['route' => 'account.orders', 'label' => 'Your Orders', 'icon' => 'icon-Package'],
        ['route' => 'account.addresses', 'label' => 'My Address', 'icon' => 'icon-Tag'],
        ['route' => 'account.settings', 'label' => 'Setting', 'icon' => 'icon-GearSix'],
    ];
@endphp

<div class="sidebar-account-wrap sidebar-content-wrap sticky-top">
    <div class="my-account-nav">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}" class="link-account {{ request()->routeIs($link['route']) ? 'active' : '' }}">
                <i class="icon {{ $link['icon'] }}"></i>
                <span class="text h6 fw-medium">{{ $link['label'] }}</span>
            </a>
        @endforeach

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="link-account w-100 text-start border-0 bg-transparent">
                <i class="icon icon-SignOut"></i>
                <span class="text h6 fw-medium">Logout</span>
            </button>
        </form>
    </div>
</div>
