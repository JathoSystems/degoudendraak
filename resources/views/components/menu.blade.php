<tr>
    <td class="menu_first_td" colspan="3">
        <img class="menu_img_left" src="{{ asset('images/dragon-small.png') }}" alt="Golden Dragon">
        <img class="menu_img_right" src="{{ asset('images/dragon-small-flipped.png') }}" alt="Golden Dragon">
        <span class="menu_first_text">{{ __('Chinees Indische Specialiteiten') }}</span><br>
        <span class="menu_second_text">{{ __('De Gouden Draak') }}</span><br>

        <div class="menu">
            <div class="menu_middle menu_gradient">
                <a href="{{ route('menukaart') }}">
                    {{ __('Menukaart') }}
                </a>
            </div>
            <div class="menu_middle menu_gradient">
                <a href="{{ route('news') }}">
                    {{ __('Nieuws') }}
                </a>
            </div>
            <div class="menu_middle menu_gradient">
                <a href="{{ route('contact') }}">
                    {{ __('Contact') }}
                </a>
            </div>
            <div class="menu_middle menu_gradient">
                <div class="language-switcher">
                    <a href="{{ route('lang.switch', 'nl') }}"
                        class="{{ app()->getLocale() == 'nl' ? 'active' : '' }}">NL</a>
                    |
                    <a href="{{ route('lang.switch', 'en') }}"
                        class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
                </div>
            </div>
        </div>

    </td>
</tr>
