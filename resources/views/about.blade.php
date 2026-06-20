@extends('layouts.base')

@section('content')

      <div class="max-w-7xl mx-auto">
                    <div class="text-center mt-8 mb-8">
                        <h2 class="text-3xl font-extrabold text-green-500">Despre Noi</h2>
                        <p class="mt-4 text-lg text-gray-700">
                            Bine ai venit pe site-ul <strong>{{ config('app.name')}}</strong>, locul unde calitatea, diversitatea și prețurile accesibile se întâlnesc pentru a-ți oferi cea mai bună experiență de cumpărături online.
                        </p>
                    </div>

                    <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Prima coloană -->
                        <div class="bg-white p-6 rounded-lg shadow-lg">
                            <h3 class="text-xl font-semibold text-green-500 mb-3">Misiunea Noastră</h3>
                            <p class="text-gray-600">
                                La <strong>{{ config('app.name')}}</strong>, ne dedicăm să oferim produse de înaltă calitate, la prețuri competitive, adaptate nevoilor fiecărui client. Indiferent dacă cauți produse pentru casă, îmbrăcăminte, electronice sau alimente, suntem aici pentru tine!
                            </p>
                        </div>

                        <!-- A doua coloană -->
                        <div class="bg-white p-6 rounded-lg shadow-lg">
                            <h3 class="text-xl font-semibold text-green-500 mb-3">Ce Ne Face Speciali?</h3>
                            <ul class="list-disc list-inside text-gray-600">
                                <li>Oferim o gamă variată de produse, atent selecționate.</li>
                                <li>Asigurăm livrare rapidă și eficientă în toată țara.</li>
                                <li>Avem prețuri accesibile și promoții exclusive.</li>
                                <li>Suntem mereu disponibili pentru asistență și suport clienți.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col md:flex-row items-center justify-center gap-8 mt-8">
                        <!-- Card Livrare Rapidă -->
                        <div class="flex items-center space-x-4 bg-white p-6 rounded-lg shadow-md">
                            <div class="text-green-500 text-3xl">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Livrare Rapidă</h4>
                                <p class="text-gray-600">Comenzile sunt livrate în cel mai scurt timp, direct la ușa ta.</p>
                            </div>
                        </div>

                        <!-- Card Produse de Calitate -->
                        <div class="flex items-center space-x-4 bg-white p-6 rounded-lg shadow-md">
                            <div class="text-green-500 text-3xl">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Calitate Garantată</h4>
                                <p class="text-gray-600">Toate produsele noastre sunt verificate pentru a asigura standarde ridicate.</p>
                            </div>
                        </div>

                        <!-- Card Serviciu Clienți -->
                        <div class="flex items-center space-x-4 bg-white p-6 rounded-lg shadow-md">
                            <div class="text-green-500 text-3xl">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Suport Clienți</h4>
                                <p class="text-gray-600">Echipa noastră este disponibilă pentru orice întrebare sau nelămurire.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 text-center">
                        <p class="text-lg text-gray-700">
                            Pentru <strong>{{ config('app.name')}}</strong>, fiecare client este important. Îți mulțumim că faci parte din comunitatea noastră!
                        </p>
                    </div>
                </div>

@endsection
