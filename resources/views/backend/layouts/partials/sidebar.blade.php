<!-- sidebar menu area start -->
<style>
    .round-logo {
        width: 100%;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>

<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}">
                <img class="round-logo" src="{{ asset('admin/assets/images/icon/logo.png') }}" alt="logo">
            </a>
        </div>
    </div>

    <div class="main-menu">
        <div class="menu-inner">
            <nav>

                @php
                    $groups = \App\Models\MenuGroup::with([
                        'menus' => function ($q) {
                            $q->whereNull('parent_id')->orderBy('order');
                        },
                    ])
                        ->orderBy('order')
                        ->get();
                @endphp

                <ul class="metismenu" id="menu">

                    @foreach ($groups as $group)
                        @foreach ($group->menus as $menu)
                            @php
                                $submenus = $menu->children;
                                $hasSub = $submenus->count() > 0;

                                $isActive = false;
                                foreach ($submenus as $sub) {
                                    if ($sub->route && request()->routeIs($sub->route)) {
                                        $isActive = true;
                                        break;
                                    }
                                }
                            @endphp

                            <li class="{{ $isActive ? 'active' : '' }}">

                                <a href="{{ $hasSub ? 'javascript:void(0)' : '#' }}"
                                    aria-expanded="{{ $isActive ? 'true' : 'false' }}">

                                    @if ($menu->icon)
                                        <i class="{{ $menu->icon }}"></i>
                                    @endif
                                    <span>{{ $menu->title }}</span>
                                </a>

                                @if ($hasSub)
                                    <ul class="collapse {{ $isActive ? 'show' : '' }}">
                                        @foreach ($submenus as $sub)
                                            <li class="{{ request()->routeIs($sub->route) ? 'active' : '' }}">
                                                <a href="{{ Route::has($sub->route) ? route($sub->route) : '#' }}">

                                                    {{ $sub->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                            </li>
                        @endforeach
                    @endforeach

                </ul>

            </nav>
        </div>
    </div>
</div>
<!-- sidebar menu area end -->
