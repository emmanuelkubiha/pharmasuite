<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOBIKO PHARMACIE - AGRO VETO | Bukavu</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#11d411",
                        "background-light": "#f6f8f6",
                        "background-dark": "#102210",
                    },
                    fontFamily: {
                        "display": ["Manrope"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem", 
                        "lg": "0.5rem", 
                        "xl": "0.75rem", 
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        html {
            scroll-behavior: smooth;
        }
        
        /* Animations d'apparition */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .fade-in-left {
            opacity: 0;
            transform: translateX(-30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .fade-in-left.visible {
            opacity: 1;
            transform: translateX(0);
        }
        
        .fade-in-right {
            opacity: 0;
            transform: translateX(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .fade-in-right.visible {
            opacity: 1;
            transform: translateX(0);
        }
        
        .scale-in {
            opacity: 0;
            transform: scale(0.9);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .scale-in.visible {
            opacity: 1;
            transform: scale(1);
        }
        
        /* Parallax effect */
        .parallax {
            transition: transform 0.1s ease-out;
        }
        
        /* Pulse animation pour CTA */
        @keyframes pulse-green {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(17, 212, 17, 0.7);
            }
            50% {
                box-shadow: 0 0 0 15px rgba(17, 212, 17, 0);
            }
        }
        .pulse-green {
            animation: pulse-green 2s infinite;
        }
        
        /* Floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-100 antialiased">

<!-- Header Navigation -->
<header class="sticky top-0 z-50 w-full bg-white/90 dark:bg-background-dark/90 backdrop-blur-md border-b border-[#e7f3e7] dark:border-gray-800">
    <div class="max-w-[1280px] mx-auto px-6 lg:px-20 h-20 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="#" title="Accueil LOBIKO" class="flex items-center gap-1 group">
                <span class="size-8 text-primary block">
                    <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor"></path>
                    </svg>
                </span>
                <h2 class="text-[#0d1b0d] dark:text-white text-xl font-bold tracking-tight ml-1 group-hover:text-primary transition-colors">
                    LOBIKO PHARMACIE <span class="text-primary">- AGRO VETO</span>
                </h2>
            </a>
        </div>
        
        <nav class="hidden md:flex items-center gap-10">
            <a class="text-[#0d1b0d] dark:text-gray-200 text-sm font-semibold hover:text-primary transition-colors" href="#">Accueil</a>
            <a class="text-[#0d1b0d] dark:text-gray-200 text-sm font-semibold hover:text-primary transition-colors" href="#about">À Propos</a>
            <a class="text-[#0d1b0d] dark:text-gray-200 text-sm font-semibold hover:text-primary transition-colors" href="#produits">Produits</a>
            <a class="text-[#0d1b0d] dark:text-gray-200 text-sm font-semibold hover:text-primary transition-colors" href="#mission">Mission</a>
            <a class="text-[#0d1b0d] dark:text-gray-200 text-sm font-semibold hover:text-primary transition-colors" href="#temoignages">Témoignages</a>
            <a class="text-[#0d1b0d] dark:text-gray-200 text-sm font-semibold hover:text-primary transition-colors" href="#contact">Contact</a>
        </nav>
        
        <a href="tel:+243997706106" class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-all shadow-md active:scale-95 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">call</span>
            Appelez-nous
        </a>
    </div>
</header>

<main>
    <!-- Hero Section avec Carrousel -->
    <section class="px-6 lg:px-20 py-8">
        <div class="max-w-[1280px] mx-auto">
            <div class="relative min-h-[560px] flex flex-col items-start justify-end p-8 lg:p-16 rounded-xl overflow-hidden bg-gray-100">
                
                <!-- Carrousel d'images -->
                <div id="carousel-pharma" class="relative w-full h-[400px] rounded-xl overflow-hidden mb-8">
                    <div class="absolute inset-0 w-full h-full">
                        <img src="images/carrousel1.jpg" class="w-full h-full object-cover absolute transition-opacity duration-700 opacity-100 carousel-img" alt="Pharmacie 1" />
                        <img src="images/carrousel3.jpg" class="w-full h-full object-cover absolute transition-opacity duration-700 opacity-0 carousel-img" alt="Pharmacie 2" />
                        <img src="images/carrousel4.jpg" class="w-full h-full object-cover absolute transition-opacity duration-700 opacity-0 carousel-img" alt="Pharmacie 3" />
                        <img src="images/carrousel5.jpg" class="w-full h-full object-cover absolute transition-opacity duration-700 opacity-0 carousel-img" alt="Pharmacie 4" />
                    </div>
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                        <button class="w-3 h-3 rounded-full bg-primary/70 carousel-dot" data-index="0"></button>
                        <button class="w-3 h-3 rounded-full bg-primary/30 carousel-dot" data-index="1"></button>
                        <button class="w-3 h-3 rounded-full bg-primary/30 carousel-dot" data-index="2"></button>
                        <button class="w-3 h-3 rounded-full bg-primary/30 carousel-dot" data-index="3"></button>
                    </div>
                </div>
                
                <!-- Texte Hero -->
                <div class="max-w-[700px] space-y-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-xl fade-in-up">
                    <h1 class="text-gray-900 dark:text-white text-4xl lg:text-6xl font-black leading-tight tracking-tight">
                        Santé & Agriculture, <br><span class="text-primary">Votre Priorité à Bukavu</span>
                    </h1>
                    <p class="text-gray-700 dark:text-gray-100 text-lg lg:text-xl font-medium max-w-2xl">
                        LOBIKO PHARMACIE - AGRO VETO vous accompagne à Bukavu : médicaments, intrants agro-vétérinaires, conseils experts, innovation digitale et accompagnement personnalisé pour la santé et l'agriculture.
                    </p>
                    <div class="pt-4">
                        <a href="#produits" class="bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-lg text-base font-bold transition-all shadow-lg inline-flex items-center gap-2 pulse-green hover:scale-105">
                            Explorer nos produits
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="px-6 lg:px-20 py-20 bg-white dark:bg-background-dark/30">
        <div class="max-w-[1280px] mx-auto grid lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-6 fade-in-left">
                <div class="inline-block px-3 py-1 bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest rounded-full">
                    LOBIKO PHARMACIE - AGRO VETO | Bukavu
                </div>
                <h2 class="text-3xl lg:text-4xl font-bold text-[#0d1b0d] dark:text-white leading-tight">
                    Votre pharmacie & agro-vétérinaire à Bukavu
                </h2>
                <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                    LOBIKO PHARMACIE - AGRO VETO est votre partenaire santé et agriculture à Bukavu, sur l'avenue P.E Lumumba, quartier Nyawera. Nous proposons médicaments, intrants vétérinaires, produits agro, conseils personnalisés et innovation digitale pour la gestion de vos besoins santé et agro-vétérinaires.
                </p>
                <div class="grid grid-cols-2 gap-6 pt-4">
                    <div class="p-4 border border-[#e7f3e7] dark:border-gray-700 rounded-lg hover:border-primary hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <span class="text-3xl font-bold text-primary animated-counter" data-target="5">0</span><span class="text-3xl font-bold text-primary">+</span>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pharmaciens, agro-vétérinaires & experts</p>
                    </div>
                    <div class="p-4 border border-[#e7f3e7] dark:border-gray-700 rounded-lg hover:border-primary hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <span class="text-3xl font-bold text-primary animated-counter" data-target="2000">0</span><span class="text-3xl font-bold text-primary">+</span>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Clients & éleveurs satisfaits à Bukavu</p>
                    </div>
                </div>
            </div>
            
            <div class="relative fade-in-right">
                <div class="w-full aspect-[4/3] rounded-xl shadow-2xl overflow-hidden bg-gray-100 hover:shadow-2xl hover:scale-105 transition-all duration-500">
                    <img src="images/image.png" alt="Équipe LOBIKO" class="w-full h-full object-contain">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-primary p-6 rounded-xl shadow-xl hidden md:flex flex-col items-center justify-center float">
                    <span class="material-symbols-outlined text-white text-4xl">medical_services</span>
                    <p class="text-white font-bold mt-2 text-center">Service d'urgence<br>24h/7j</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Nos Produits Section -->
    <section id="produits" class="px-6 lg:px-20 py-24 bg-background-light dark:bg-background-dark">
        <div class="max-w-[1280px] mx-auto">
            <div class="text-center mb-16 space-y-4 fade-in-up">
                <h2 class="text-3xl lg:text-4xl font-bold text-[#0d1b0d] dark:text-white">Nos Produits</h2>
                <div class="w-20 h-1.5 bg-primary mx-auto rounded-full"></div>
                <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                    Découvrez notre sélection de produits pharmaceutiques, vétérinaires et agricoles : médicaments, intrants, nutrition animale, phytosanitaires, compléments, hygiène et bien plus pour la santé et l'agriculture à Bukavu.
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-10">
                <div class="product-card bg-gradient-to-br from-white to-green-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl shadow-lg p-8 flex flex-col items-center text-center border-2 border-[#cfe7cf] dark:border-gray-700 hover:border-primary hover:shadow-2xl hover:scale-105 transition-all duration-500 fade-in-up" style="transition-delay: 0.1s;">
                    <div class="bg-primary/10 p-4 rounded-full mb-4 group-hover:rotate-12 transition-transform duration-500">
                        <span class="material-symbols-outlined text-primary text-5xl">medication</span>
                    </div>
                    <h3 class="font-bold text-xl mb-2 text-[#0d1b0d] dark:text-white">Médicaments & Parapharmacie</h3>
                    <p class="text-gray-600 dark:text-gray-300">Antibiotiques, antalgiques, vitamines, soins courants, produits bébé, hygiène, etc.</p>
                </div>
                
                <div class="product-card bg-gradient-to-br from-white to-green-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl shadow-lg p-8 flex flex-col items-center text-center border-2 border-[#cfe7cf] dark:border-gray-700 hover:border-primary hover:shadow-2xl hover:scale-105 transition-all duration-500 fade-in-up" style="transition-delay: 0.2s;">
                    <div class="bg-primary/10 p-4 rounded-full mb-4 group-hover:rotate-12 transition-transform duration-500">
                        <span class="material-symbols-outlined text-primary text-5xl">pets</span>
                    </div>
                    <h3 class="font-bold text-xl mb-2 text-[#0d1b0d] dark:text-white">Produits Vétérinaires</h3>
                    <p class="text-gray-600 dark:text-gray-300">Vermifuges, vaccins, nutrition animale, soins pour bétail, volailles, chiens, chats…</p>
                </div>
                
                <div class="product-card bg-gradient-to-br from-white to-green-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl shadow-lg p-8 flex flex-col items-center text-center border-2 border-[#cfe7cf] dark:border-gray-700 hover:border-primary hover:shadow-2xl hover:scale-105 transition-all duration-500 fade-in-up" style="transition-delay: 0.3s;">
                    <div class="bg-primary/10 p-4 rounded-full mb-4 group-hover:rotate-12 transition-transform duration-500">
                        <span class="material-symbols-outlined text-primary text-5xl">eco</span>
                    </div>
                    <h3 class="font-bold text-xl mb-2 text-[#0d1b0d] dark:text-white">Intrants & Produits Agricoles</h3>
                    <p class="text-gray-600 dark:text-gray-300">Semences, engrais, phytosanitaires, outils, conseils pour agriculteurs et éleveurs.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section id="mission" class="px-6 lg:px-20 py-24 bg-white dark:bg-background-dark/30">
        <div class="max-w-[1280px] mx-auto">
            <div class="text-center mb-16 space-y-4 fade-in-up">
                <h2 class="text-3xl lg:text-4xl font-bold text-[#0d1b0d] dark:text-white">Notre engagement</h2>
                <div class="w-20 h-1.5 bg-primary mx-auto rounded-full"></div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Mission Card -->
                <div class="group p-8 lg:p-12 rounded-2xl border-2 border-[#cfe7cf] dark:border-gray-700 bg-gradient-to-br from-white to-green-50 dark:from-gray-900 dark:to-gray-800 hover:border-primary hover:shadow-2xl hover:scale-105 transition-all duration-500 fade-in-left">
                    <div class="size-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-8 group-hover:scale-125 group-hover:rotate-12 transition-all duration-500">
                        <span class="material-symbols-outlined text-[32px]">person_heart</span>
                    </div>
                    <h3 class="text-2xl font-bold text-[#0d1b0d] dark:text-white mb-4">Notre Mission</h3>
                    <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                        Offrir un accompagnement santé personnalisé, des conseils experts et un accès rapide à vos traitements. LOBIKO s'engage à la transparence, la sécurité et l'innovation pour chaque patient.
                    </p>
                </div>
                
                <!-- Vision Card -->
                <div class="group p-8 lg:p-12 rounded-2xl border-2 border-[#cfe7cf] dark:border-gray-700 bg-gradient-to-br from-white to-green-50 dark:from-gray-900 dark:to-gray-800 hover:border-primary hover:shadow-2xl hover:scale-105 transition-all duration-500 fade-in-right">
                    <div class="size-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-8 group-hover:scale-125 group-hover:rotate-12 transition-all duration-500">
                        <span class="material-symbols-outlined text-[32px]">visibility</span>
                    </div>
                    <h3 class="text-2xl font-bold text-[#0d1b0d] dark:text-white mb-4">Notre Vision</h3>
                    <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                        Être la référence pharmaceutique à Bukavu, en alliant proximité, innovation digitale et excellence humaine. Nous voulons simplifier la gestion de votre santé et accompagner le développement agricole de la région.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Témoignages Clients Section -->
    <section id="temoignages" class="px-6 lg:px-20 py-24 bg-background-light dark:bg-background-dark overflow-hidden">
        <div class="max-w-[1280px] mx-auto">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-3xl lg:text-4xl font-bold text-[#0d1b0d] dark:text-white">Ils nous font confiance</h2>
                <div class="w-20 h-1.5 bg-primary mx-auto rounded-full"></div>
                <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">Découvrez les témoignages de nos clients satisfaits à Bukavu</p>
            </div>

            <!-- Carousel Container -->
            <div class="relative">
                <div class="testimonials-carousel flex transition-transform duration-700 ease-in-out" style="gap: 2rem;">
                <!-- Témoignage 1 -->
                <div class="testimonial-card min-w-full md:min-w-[calc(33.333%-1.35rem)] bg-gradient-to-br from-primary/20 via-primary/10 to-green-50 dark:from-primary/30 dark:via-primary/20 dark:to-gray-900 rounded-2xl p-8 border-2 border-primary/30 dark:border-primary/40 hover:shadow-2xl hover:scale-105 transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-md">
                            <span class="material-symbols-outlined text-primary text-2xl">person</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#0d1b0d] dark:text-white">Jean-Pierre M.</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Éleveur, Bukavu</p>
                        </div>
                    </div>
                    <div class="flex gap-1 mb-4">
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                    </div>
                    <p class="text-gray-800 dark:text-gray-200 italic font-medium">"Excellent service ! Les produits vétérinaires sont de qualité et les conseils très professionnels. Mes bovins se portent mieux depuis que je me fournis chez LOBIKO."</p>
                </div>

                <!-- Témoignage 2 -->
                <div class="testimonial-card min-w-full md:min-w-[calc(33.333%-1.35rem)] bg-gradient-to-br from-primary/20 via-primary/10 to-green-50 dark:from-primary/30 dark:via-primary/20 dark:to-gray-900 rounded-2xl p-8 border-2 border-primary/30 dark:border-primary/40 hover:shadow-2xl hover:scale-105 transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-md">
                            <span class="material-symbols-outlined text-primary text-2xl">person</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#0d1b0d] dark:text-white">Marie K.</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Maman, Ibanda</p>
                        </div>
                    </div>
                    <div class="flex gap-1 mb-4">
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                    </div>
                    <p class="text-gray-800 dark:text-gray-200 italic font-medium">"Pharmacie de confiance ! J'y trouve tous les médicaments pour ma famille. L'équipe est accueillante et prend le temps d'expliquer les posologies. Merci LOBIKO !"</p>
                </div>

                <!-- Témoignage 3 -->
                <div class="testimonial-card min-w-full md:min-w-[calc(33.333%-1.35rem)] bg-gradient-to-br from-primary/20 via-primary/10 to-green-50 dark:from-primary/30 dark:via-primary/20 dark:to-gray-900 rounded-2xl p-8 border-2 border-primary/30 dark:border-primary/40 hover:shadow-2xl hover:scale-105 transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-md">
                            <span class="material-symbols-outlined text-primary text-2xl">person</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#0d1b0d] dark:text-white">Augustin B.</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Agriculteur, Kabare</p>
                        </div>
                    </div>
                    <div class="flex gap-1 mb-4">
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                    </div>
                    <p class="text-gray-800 dark:text-gray-200 italic font-medium">"Les intrants agricoles de LOBIKO ont transformé mes récoltes ! Engrais efficaces, semences de qualité et prix abordables. Je recommande vivement !"</p>
                </div>

                <!-- Témoignage 4 - Swahili -->
                <div class="testimonial-card min-w-full md:min-w-[calc(33.333%-1.35rem)] bg-gradient-to-br from-primary/20 via-primary/10 to-green-50 dark:from-primary/30 dark:via-primary/20 dark:to-gray-900 rounded-2xl p-8 border-2 border-primary/30 dark:border-primary/40 hover:shadow-2xl hover:scale-105 transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-md">
                            <span class="material-symbols-outlined text-primary text-2xl">person</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#0d1b0d] dark:text-white">Zawadi N.</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Mama Biashara, Nyawera</p>
                        </div>
                    </div>
                    <div class="flex gap-1 mb-4">
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                    </div>
                    <p class="text-gray-800 dark:text-gray-200 italic font-medium">"Dawa zote zinapatikana kwa urahisi na bei nafuu. Watumishi wanakaribishwa sana na wanajua kazi yao vizuri. Asante sana LOBIKO, mko tayari kila wakati!"</p>
                </div>

                <!-- Témoignage 5 - Swahili -->
                <div class="testimonial-card min-w-full md:min-w-[calc(33.333%-1.35rem)] bg-gradient-to-br from-primary/20 via-primary/10 to-green-50 dark:from-primary/30 dark:via-primary/20 dark:to-gray-900 rounded-2xl p-8 border-2 border-primary/30 dark:border-primary/40 hover:shadow-2xl hover:scale-105 transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-md">
                            <span class="material-symbols-outlined text-primary text-2xl">person</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#0d1b0d] dark:text-white">Bahati K.</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Mfugaji, Kabare</p>
                        </div>
                    </div>
                    <div class="flex gap-1 mb-4">
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-yellow-500 text-xl">★</span>
                    </div>
                    <p class="text-gray-800 dark:text-gray-200 italic font-medium">"Wanauza bidhaa bora za mifugo na kilimo. Kuku zangu na ng'ombe wamekuwa wenye afya zaidi. Napendekeza LOBIKO kwa wafugaji wote wa Bukavu!"</p>
                </div>
                
            </div>
            
            <!-- Navigation Arrows -->
            <button id="prev-testimonial" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 bg-primary hover:bg-primary/90 text-white p-3 rounded-full shadow-xl z-10 transition-all hover:scale-110">
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button id="next-testimonial" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 bg-primary hover:bg-primary/90 text-white p-3 rounded-full shadow-xl z-10 transition-all hover:scale-110">
                <span class="material-symbols-outlined">chevron_right</span>
            </button>
            
            <!-- Dots Indicators -->
            <div class="flex justify-center gap-2 mt-8">
                <button class="testimonial-dot w-3 h-3 rounded-full bg-primary transition-all" data-index="0"></button>
                <button class="testimonial-dot w-3 h-3 rounded-full bg-gray-300 transition-all" data-index="1"></button>
                <button class="testimonial-dot w-3 h-3 rounded-full bg-gray-300 transition-all" data-index="2"></button>
                <button class="testimonial-dot w-3 h-3 rounded-full bg-gray-300 transition-all" data-index="3"></button>
                <button class="testimonial-dot w-3 h-3 rounded-full bg-gray-300 transition-all" data-index="4"></button>
            </div>
        </div>

            <!-- Statistiques de satisfaction -->
            <div class="mt-16 grid md:grid-cols-4 gap-6">
                <div class="text-center p-6 bg-gradient-to-br from-white to-green-50 dark:from-gray-900 dark:to-gray-800 rounded-xl border-2 border-primary/30 dark:border-primary/40 hover:scale-110 hover:shadow-xl transition-all duration-500 scale-in">
                    <div class="text-4xl font-bold text-primary mb-2"><span class="animated-counter" data-target="98">0</span>%</div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">Clients Satisfaits</p>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-white to-green-50 dark:from-gray-900 dark:to-gray-800 rounded-xl border-2 border-primary/30 dark:border-primary/40 hover:scale-110 hover:shadow-xl transition-all duration-500 scale-in" style="transition-delay: 0.1s;">
                    <div class="text-4xl font-bold text-primary mb-2"><span class="animated-counter" data-target="2000">0</span>+</div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">Clients Servis</p>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-white to-green-50 dark:from-gray-900 dark:to-gray-800 rounded-xl border-2 border-primary/30 dark:border-primary/40 hover:scale-110 hover:shadow-xl transition-all duration-500 scale-in" style="transition-delay: 0.2s;">
                    <div class="text-4xl font-bold text-primary mb-2"><span class="animated-counter" data-target="5">0</span>+</div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">Années d'Expérience</p>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-white to-green-50 dark:from-gray-900 dark:to-gray-800 rounded-xl border-2 border-primary/30 dark:border-primary/40 hover:scale-110 hover:shadow-xl transition-all duration-500 scale-in" style="transition-delay: 0.3s;">
                    <div class="text-4xl font-bold text-primary mb-2">24/7</div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">Service d'Urgence</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="px-6 lg:px-20 py-24 bg-background-light dark:bg-background-dark">
        <div class="max-w-[960px] mx-auto fade-in-up">
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl overflow-hidden border-2 border-primary/20 dark:border-primary/30 hover:shadow-2xl transition-all duration-500">
                <div class="grid md:grid-cols-2">
                    <!-- Contact Info -->
                    <div class="p-10 lg:p-14 space-y-8 bg-primary text-white">
                        <h2 class="text-3xl font-bold">Contactez-nous</h2>
                        <p class="text-white/80">Une question santé, un besoin agro-vétérinaire ? Contactez-nous pour conseils, commandes ou accompagnement personnalisé à Bukavu.</p>
                        
                        <div class="space-y-4">
                            <a class="flex items-center gap-4 hover:bg-white/10 p-3 rounded-lg transition-colors" href="tel:+243997706106">
                                <span class="material-symbols-outlined">phone</span>
                                <span>Appelez-nous</span>
                            </a>
                            <a class="flex items-center gap-4 hover:bg-white/10 p-3 rounded-lg transition-colors" href="tel:+243997706106">
                                <span class="material-symbols-outlined">call</span>
                                <span>+243 997 706 106</span>
                            </a>
                            <a class="flex items-center gap-4 hover:bg-white/10 p-3 rounded-lg transition-colors" target="_blank" rel="noopener" href="https://wa.me/243997706106?text=Bonjour%20Pharmacie%20LOBIKO%2C%20j%E2%80%99aimerais%20avoir%20des%20informations%20sur%20vos%20services.%20Merci!">
                                <span class="material-symbols-outlined">chat</span>
                                <span>Discutez avec nous sur WhatsApp</span>
                            </a>
                            <a class="flex items-center gap-4 hover:bg-white/10 p-3 rounded-lg transition-colors" href="mailto:pharmacielobiko@yahoo.fr">
                                <span class="material-symbols-outlined">mail</span>
                                <span>pharmacielobiko@yahoo.fr</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Contact Form -->
                    <div class="p-10 lg:p-14">
                        <form class="space-y-4" id="contact-whatsapp-form" autocomplete="off">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nom complet</label>
                                <input 
                                    name="nom" 
                                    required 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                                    placeholder="Emmanuel Kubiha" 
                                    type="text"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                                <input 
                                    name="telephone" 
                                    required 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                                    placeholder="+243 000 000 000" 
                                    type="tel"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input 
                                    name="email" 
                                    required 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                                    placeholder="emmanuelkubiha@email.com" 
                                    type="email"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Message</label>
                                <textarea 
                                    name="message" 
                                    required 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                                    placeholder="Votre message ici..." 
                                    rows="4"
                                ></textarea>
                            </div>
                            <button class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-lg transition-all shadow-md" type="submit">
                                Envoyer sur WhatsApp
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Visit Us Section -->
    <section class="px-6 lg:px-20 py-24 bg-white dark:bg-background-dark/30">
        <div class="max-w-[1280px] mx-auto">
            <div class="flex flex-col lg:flex-row gap-12">
                <div class="lg:w-5/12 space-y-8 fade-in-left">
                    <div>
                        <h2 class="text-3xl font-bold text-[#0d1b0d] dark:text-white mb-4">Nous Visiter</h2>
                        <p class="text-gray-600 dark:text-gray-400">Passez nous voir à Bukavu pour des conseils santé, agro et vétérinaires, ou pour récupérer vos produits en toute sérénité.</p>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-primary bg-primary/10 p-2 rounded-lg">location_on</span>
                            <div>
                                <h4 class="font-bold text-[#0d1b0d] dark:text-white">Adresse</h4>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">163, AV P.E. Lumumba, Ibanda, Bukavu/RDC</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-primary bg-primary/10 p-2 rounded-lg">schedule</span>
                            <div>
                                <h4 class="font-bold text-[#0d1b0d] dark:text-white">Horaires d'ouverture</h4>
                                <ul class="text-gray-500 dark:text-gray-400 text-sm space-y-1">
                                    <li class="flex justify-between w-64">
                                        <span>Lundi - Vendredi</span> 
                                        <span>08h00 - 20h00</span>
                                    </li>
                                    <li class="flex justify-between w-64">
                                        <span>Samedi</span> 
                                        <span>09h00 - 18h00</span>
                                    </li>
                                    <li class="flex justify-between w-64 text-primary font-semibold">
                                        <span>Dimanche</span> 
                                        <span>Fermé (Garde sur appel)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-7/12 fade-in-right">
                    <div class="w-full h-[400px] rounded-2xl overflow-hidden shadow-xl bg-gray-100 dark:bg-gray-800 hover:shadow-2xl hover:scale-105 transition-all duration-500">
                        <iframe 
                            src="https://www.google.com/maps?hl=fr&q=Nyawera,+Bukavu,+RDC&t=&z=16&ie=UTF8&iwloc=&output=embed" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            class="rounded-2xl">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- Footer -->
<footer class="bg-white dark:bg-background-dark border-t border-[#e7f3e7] dark:border-gray-800 px-6 lg:px-20 py-12">
    <div class="max-w-[1280px] mx-auto flex flex-col md:flex-row justify-between items-center gap-6 py-8">
        <div class="flex items-center gap-3">
            <a href="bienvenue.php" title="Démarrer le système" class="flex items-center gap-2 group">
                <span class="size-8 text-primary block">
                    <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor"></path>
                    </svg>
                </span>
                <span class="font-bold text-lg dark:text-white group-hover:text-primary transition-colors">
                    LOBIKO PHARMACIE <span class="text-primary">- AGRO VETO</span>
                </span>
            </a>
        </div>
        
        <div class="flex gap-4">
            <a class="size-10 rounded-full border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white hover:border-primary transition-all" href="https://facebook.com/" target="_blank" rel="noopener" title="Facebook">
                <svg class="size-5 fill-current" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path>
                </svg>
            </a>
            <a class="size-10 rounded-full border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white hover:border-primary transition-all" href="https://wa.me/243997706106" target="_blank" rel="noopener" title="WhatsApp">
                <svg class="size-5 fill-current" viewBox="0 0 32 32">
                    <path d="M16 3C9.373 3 4 8.373 4 15c0 2.637.86 5.08 2.48 7.13L4 29l7.13-2.48A11.93 11.93 0 0 0 16 27c6.627 0 12-5.373 12-12S22.627 3 16 3zm0 21.5c-2.07 0-4.07-.54-5.8-1.57l-.41-.24-4.13 1.44 1.44-4.13-.24-.41A9.48 9.48 0 0 1 6.5 15c0-5.24 4.26-9.5 9.5-9.5s9.5 4.26 9.5 9.5-4.26 9.5-9.5 9.5zm5.07-7.13c-.28-.14-1.65-.81-1.9-.9-.25-.09-.43-.14-.61.14-.18.28-.7.9-.86 1.08-.16.18-.32.2-.6.07-.28-.14-1.18-.44-2.25-1.4-.83-.74-1.39-1.65-1.55-1.93-.16-.28-.02-.43.12-.57.13-.13.28-.34.42-.51.14-.17.18-.29.28-.48.09-.19.05-.36-.02-.5-.07-.14-.61-1.47-.84-2.01-.22-.54-.45-.47-.61-.48-.16-.01-.36-.01-.56-.01-.19 0-.5.07-.76.36-.26.29-1 1-.97 2.43.03 1.43 1.03 2.81 1.18 3 .15.19 2.03 3.1 4.93 4.22.69.28 1.23.45 1.65.58.69.22 1.32.19 1.81.12.55-.08 1.65-.67 1.89-1.32.23-.65.23-1.2.16-1.32-.07-.12-.25-.19-.53-.33z"></path>
                </svg>
            </a>
            <a class="size-10 rounded-full border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white hover:border-primary transition-all" href="mailto:pharmacielobiko@yahoo.fr" title="Email">
                <svg class="size-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 13.065l-8-5.065V19a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8l-8 5.065zM21 6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v.217l9 5.7 9-5.7V6z"></path>
                </svg>
            </a>
            <a class="size-10 rounded-full border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white hover:border-primary transition-all" href="https://instagram.com/" target="_blank" rel="noopener" title="Instagram">
                <svg class="size-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                </svg>
            </a>
        </div>
        
        <div class="text-xs text-gray-400 text-center md:text-right">
            Tous droits réservés à PharmaSuite – <a href="https://www.linkedin.com/in/emmanuel-baraka/" target="_blank" rel="noopener" class="text-primary font-semibold hover:underline">Développeur</a>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script>
// Carrousel JS
const imgs = document.querySelectorAll('.carousel-img');
const dots = document.querySelectorAll('.carousel-dot');
let current = 0;

function showSlide(idx) {
    imgs.forEach((img, i) => {
        img.style.opacity = (i === idx ? '1' : '0');
    });
    dots.forEach((dot, i) => {
        dot.classList.toggle('bg-primary/70', i === idx);
        dot.classList.toggle('bg-primary/30', i !== idx);
    });
    current = idx;
}

dots.forEach((dot, i) => {
    dot.addEventListener('click', () => showSlide(i));
});

setInterval(() => {
    showSlide((current + 1) % imgs.length);
}, 5000);

showSlide(0);

// Testimonials Carousel Animation
const testimonialCarousel = document.querySelector('.testimonials-carousel');
const testimonialCards = document.querySelectorAll('.testimonial-card');
const testimonialDots = document.querySelectorAll('.testimonial-dot');
const prevBtn = document.getElementById('prev-testimonial');
const nextBtn = document.getElementById('next-testimonial');
let currentTestimonial = 0;
const totalTestimonials = testimonialCards.length;

function getVisibleCards() {
    return window.innerWidth >= 768 ? 3 : 1;
}

function updateTestimonialPosition() {
    const visibleCards = getVisibleCards();
    const maxIndex = totalTestimonials - visibleCards;
    
    if (currentTestimonial > maxIndex) {
        currentTestimonial = maxIndex;
    }
    if (currentTestimonial < 0) {
        currentTestimonial = 0;
    }
    
    const offset = currentTestimonial * (100 / visibleCards);
    testimonialCarousel.style.transform = `translateX(-${offset}%)`;
    
    // Update dots
    testimonialDots.forEach((dot, index) => {
        if (index === currentTestimonial) {
            dot.classList.remove('bg-gray-300');
            dot.classList.add('bg-primary', 'scale-125');
        } else {
            dot.classList.remove('bg-primary', 'scale-125');
            dot.classList.add('bg-gray-300');
        }
    });
}

function nextTestimonial() {
    const visibleCards = getVisibleCards();
    const maxIndex = totalTestimonials - visibleCards;
    currentTestimonial = currentTestimonial >= maxIndex ? 0 : currentTestimonial + 1;
    updateTestimonialPosition();
}

function prevTestimonial() {
    const visibleCards = getVisibleCards();
    const maxIndex = totalTestimonials - visibleCards;
    currentTestimonial = currentTestimonial <= 0 ? maxIndex : currentTestimonial - 1;
    updateTestimonialPosition();
}

// Event listeners
prevBtn.addEventListener('click', () => {
    prevTestimonial();
    clearInterval(testimonialInterval);
    testimonialInterval = setInterval(nextTestimonial, 5000);
});

nextBtn.addEventListener('click', () => {
    nextTestimonial();
    clearInterval(testimonialInterval);
    testimonialInterval = setInterval(nextTestimonial, 5000);
});

testimonialDots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        currentTestimonial = index;
        updateTestimonialPosition();
        clearInterval(testimonialInterval);
        testimonialInterval = setInterval(nextTestimonial, 5000);
    });
});

// Auto-rotate every 5 seconds
let testimonialInterval = setInterval(nextTestimonial, 5000);

// Update on window resize
window.addEventListener('resize', updateTestimonialPosition);

// Initialize
updateTestimonialPosition();

// Intersection Observer pour les animations au scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            
            // Animer les compteurs quand ils deviennent visibles
            if (entry.target.classList.contains('animated-counter') && !entry.target.hasAttribute('data-animated')) {
                animateCounter(entry.target);
                entry.target.setAttribute('data-animated', 'true');
            }
        }
    });
}, observerOptions);

// Observer tous les éléments animés
document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .scale-in').forEach(el => {
    observer.observe(el);
});

// Observer tous les compteurs
document.querySelectorAll('.animated-counter').forEach(counter => {
    observer.observe(counter);
});

// Animation des compteurs au scroll
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000; // 2 secondes
    const steps = 60;
    const stepValue = target / steps;
    let current = 0;
    let step = 0;
    
    const interval = setInterval(() => {
        step++;
        current += stepValue;
        
        if (step >= steps) {
            element.textContent = target;
            clearInterval(interval);
        } else {
            element.textContent = Math.floor(current);
        }
    }, duration / steps);
}

// Effet parallaxe léger sur le hero
let lastScrollY = window.scrollY;
window.addEventListener('scroll', () => {
    const heroSection = document.querySelector('#carousel-pharma');
    if (heroSection) {
        const scrolled = window.scrollY;
        const parallaxSpeed = 0.5;
        heroSection.style.transform = `translateY(${scrolled * parallaxSpeed}px)`;
    }
});

// Animation d'apparition du hero text au chargement
window.addEventListener('load', () => {
    const heroText = document.querySelector('.fade-in-up');
    if (heroText) {
        setTimeout(() => {
            heroText.classList.add('visible');
        }, 300);
    }
});

// WhatsApp Contact Form
document.getElementById('contact-whatsapp-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const nom = this.nom.value.trim();
    const telephone = this.telephone.value.trim();
    const email = this.email.value.trim();
    const msg = this.message.value.trim();
    const whatsapp = '243997706106';
    const text = encodeURIComponent('Bonjour LOBIKO PHARMACIE - AGRO VETO Bukavu,\nNom: ' + nom + '\nTéléphone: ' + telephone + '\nEmail: ' + email + '\nMessage: ' + msg);
    const url = 'https://wa.me/' + whatsapp + '?text=' + text;
    window.open(url, '_blank');
});
</script>

</body>
</html>
