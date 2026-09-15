<section class="nav-group {{ $group['open'] ? 'open' : '' }}" data-nav-group="{{ $key }}">
    <button class="nav-group-toggle" type="button" aria-expanded="{{ $group['open'] ? 'true' : 'false' }}">
        <span><i class="fas {{ $group['icon'] }}"></i> {{ $group['label'] }}</span><i class="fas fa-chevron-left arrow"></i>
    </button>
    <div class="submenu" @unless($group['open']) hidden @endunless>
        @foreach($items as $item)
            @can($item['permission'])
                <a data-nav-item="{{ $item['key'] }}" class="{{ request()->routeIs(...$item['patterns']) ? 'active' : '' }}" href="{{ route($item['route']) }}"><i class="fas {{ $item['icon'] }}"></i> {{ $item['label'] }}</a>
            @endcan
        @endforeach
    </div>
</section>
