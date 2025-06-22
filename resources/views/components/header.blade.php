<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/menukaart_favorieten.js') }}"></script>
    @vite(['resources/js/app.js'])
</head>
<body>
<table class="main_table">
    <tr class="first_tr">
        <td class="dragons_td">
            <a href="/" class="full-link">
                <img class="header_img" src="{{ asset('images/dragon-small.png') }}" alt="Golden Dragon">
                <span class="golden_dragon_text">{{ __('De Gouden Draak') }}</span>
                <img class="header_img" src="{{ asset('images/dragon-small-flipped.png') }}" alt="Golden Dragon">
            </a>
        </td>
        <td class="scrolling-text-holder">
            <a href="/">
                <p class="scrolling-text">
                    {{ __('Welkom bij De Gouden Draak. Klik op deze tekst om de aanbiedingen van deze week te zien!') }}
                </p>
            </a>
        </td>
        <td class="dragons_td">
            <a href="/" class="full-link">
                <img class="header_img" src="{{ asset('images/dragon-small.png') }}" alt="Golden Dragon">
                <span class="golden_dragon_text">{{ __('De Gouden Draak') }}</span>
                <img class="header_img" src="{{ asset('images/dragon-small-flipped.png') }}" alt="Golden Dragon">
            </a>
        </td>
    </tr>
</table>
</body>
</html>
