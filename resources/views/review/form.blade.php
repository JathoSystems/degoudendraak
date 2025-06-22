<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beoordeel uw ervaring - De Gouden Draak</title>
    @vite(['resources/css/app.css'])
    <style>
        /* Custom styles for star rating */
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            padding-bottom: 1rem;
        }

        .rating > input {
            display: none;
        }

        .rating > label {
            position: relative;
            width: 1.1em;
            font-size: 2.5rem;
            color: #ffd600;
            cursor: pointer;
        }

        .rating > label::before {
            content: "\2605";
            position: absolute;
            opacity: 0.25;
        }

        .rating > label:hover:before,
        .rating > label:hover ~ label:before {
            opacity: 1 !important;
        }

        .rating > input:checked ~ label:before {
            opacity: 1;
        }

        .rating:hover > input:checked ~ label:before {
            opacity: 0.4;
        }

        /* Animation effects */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
<div class="max-w-2xl p-6 mx-auto">
    <div class="mb-8 text-center animate-fadeIn">
        <h1 class="text-3xl font-bold text-indigo-600">De Gouden Draak</h1>
        <p class="text-gray-600">Chinese Restaurant</p>
    </div>

    <div class="p-8 mb-6 delay-100 bg-white rounded-lg shadow-lg animate-fadeIn">
        <h2 class="mb-6 text-2xl font-bold text-center text-gray-800">Hoe was uw ervaring?</h2>
        <p class="mb-6 text-center text-gray-600">Uw feedback helpt ons om onze service te verbeteren!</p>

        <form action="{{ route('review.submit', ['reviewCode' => $reviewCode]) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Personal Info (Optional) -->
            <div class="space-y-4 delay-200 animate-fadeIn">
                <h3 class="pb-2 text-lg font-semibold text-gray-700 border-b">Uw Gegevens (Optioneel)</h3>

                <div>
                    <label for="name" class="block mb-1 text-sm font-medium text-gray-700">Naam</label>
                    <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200" placeholder="Uw naam">
                </div>

                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">E-mail</label>
                    <input type="email" name="email" id="email" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200" placeholder="Uw e-mailadres">
                    <p class="mt-1 text-xs text-gray-500">We sturen u alleen een bevestiging van uw korting</p>
                </div>
            </div>

            <!-- Ratings -->
            <div class="delay-300 animate-fadeIn">
                <h3 class="pb-2 text-lg font-semibold text-gray-700 border-b">Beoordeel Uw Ervaring</h3>

                <div class="pb-12">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Eten</label>
                    <div class="rating">
                        <input type="radio" id="food5" name="food_rating" value="5" required />
                        <label for="food5" title="5 sterren"></label>
                        <input type="radio" id="food4" name="food_rating" value="4" />
                        <label for="food4" title="4 sterren"></label>
                        <input type="radio" id="food3" name="food_rating" value="3" />
                        <label for="food3" title="3 sterren"></label>
                        <input type="radio" id="food2" name="food_rating" value="2" />
                        <label for="food2" title="2 sterren"></label>
                        <input type="radio" id="food1" name="food_rating" value="1" />
                        <label for="food1" title="1 ster"></label>
                    </div>
                    @error('food_rating')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pb-12">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Service</label>
                    <div class="rating">
                        <input type="radio" id="service5" name="service_rating" value="5" required />
                        <label for="service5" title="5 sterren"></label>
                        <input type="radio" id="service4" name="service_rating" value="4" />
                        <label for="service4" title="4 sterren"></label>
                        <input type="radio" id="service3" name="service_rating" value="3" />
                        <label for="service3" title="3 sterren"></label>
                        <input type="radio" id="service2" name="service_rating" value="2" />
                        <label for="service2" title="2 sterren"></label>
                        <input type="radio" id="service1" name="service_rating" value="1" />
                        <label for="service1" title="1 ster"></label>
                    </div>
                    @error('service_rating')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pb-12">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Sfeer</label>
                    <div class="rating">
                        <input type="radio" id="ambiance5" name="ambiance_rating" value="5" required />
                        <label for="ambiance5" title="5 sterren"></label>
                        <input type="radio" id="ambiance4" name="ambiance_rating" value="4" />
                        <label for="ambiance4" title="4 sterren"></label>
                        <input type="radio" id="ambiance3" name="ambiance_rating" value="3" />
                        <label for="ambiance3" title="3 sterren"></label>
                        <input type="radio" id="ambiance2" name="ambiance_rating" value="2" />
                        <label for="ambiance2" title="2 sterren"></label>
                        <input type="radio" id="ambiance1" name="ambiance_rating" value="1" />
                        <label for="ambiance1" title="1 ster"></label>
                    </div>
                    @error('ambiance_rating')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pb-12">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Algemene Ervaring</label>
                    <div class="rating">
                        <input type="radio" id="overall5" name="overall_rating" value="5" required />
                        <label for="overall5" title="5 sterren"></label>
                        <input type="radio" id="overall4" name="overall_rating" value="4" />
                        <label for="overall4" title="4 sterren"></label>
                        <input type="radio" id="overall3" name="overall_rating" value="3" />
                        <label for="overall3" title="3 sterren"></label>
                        <input type="radio" id="overall2" name="overall_rating" value="2" />
                        <label for="overall2" title="2 sterren"></label>
                        <input type="radio" id="overall1" name="overall_rating" value="1" />
                        <label for="overall1" title="1 ster"></label>
                    </div>
                    @error('overall_rating')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Additional Questions -->
            <div class="space-y-4 animate-fadeIn delay-400">
                <h3 class="pb-2 text-lg font-semibold text-gray-700 border-b">Vertel ons meer</h3>

                <div>
                    <label for="favorite_dish" class="block mb-1 text-sm font-medium text-gray-700">Wat was uw favoriete gerecht?</label>
                    <input type="text" name="favorite_dish" id="favorite_dish" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200" placeholder="Bijvoorbeeld: Babi Pangang">
                </div>

                <div>
                    <label for="improvement_area" class="block mb-1 text-sm font-medium text-gray-700">Wat kunnen we verbeteren?</label>
                    <select name="improvement_area" id="improvement_area" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200">
                        <option value="">Selecteer een optie</option>
                        <option value="food">Eten</option>
                        <option value="service">Service</option>
                        <option value="ambiance">Sfeer</option>
                        <option value="cleanliness">Hygiëne</option>
                        <option value="prices">Prijzen</option>
                        <option value="nothing">Niets, alles was perfect</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Zou u terugkomen?</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="would_return" value="1" required class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2">Ja</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="would_return" value="0" required class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2">Nee</span>
                        </label>
                    </div>
                    @error('would_return')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="heard_about_us" class="block mb-1 text-sm font-medium text-gray-700">Hoe heeft u over ons gehoord?</label>
                    <select name="heard_about_us" id="heard_about_us" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200">
                        <option value="">Selecteer een optie</option>
                        <option value="friends">Via vrienden of familie</option>
                        <option value="social_media">Social media</option>
                        <option value="google">Google</option>
                        <option value="walk_by">Toevallig langsgekomen</option>
                        <option value="returning">Ik ben een vaste klant</option>
                        <option value="other">Anders</option>
                    </select>
                </div>

                <div>
                    <label for="feedback" class="block mb-1 text-sm font-medium text-gray-700">Aanvullende feedback</label>
                    <textarea name="feedback" id="feedback" rows="4" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200" placeholder="Vertel ons meer over uw ervaring..."></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="px-6 py-3 font-bold text-white transition-colors bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    Verstuur Beoordeling
                </button>
                <p class="mt-2 text-sm text-gray-600">En ontvang 10% korting bij uw volgende bezoek!</p>
            </div>
        </form>
    </div>

    <div class="text-sm text-center text-gray-500 animate-fadeIn">
        <p>De Gouden Draak</p>
        <p>Tel: +31 06 12345678 • info@degoudendraak.nl</p>
    </div>
</div>

<script>
    // Add simple animation effects when scrolling
    document.addEventListener('DOMContentLoaded', function() {
        const animateElements = document.querySelectorAll('.animate-fadeIn');

        animateElements.forEach(element => {
            element.style.opacity = '0';
        });

        // Trigger animations
        setTimeout(() => {
            animateElements.forEach(element => {
                element.style.opacity = '1';
            });
        }, 100);
    });
</script>
</body>
</html>
