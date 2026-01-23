<!DOCTYPE html>
<html class="light" lang="fr"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Pharmacie Showcase - Votre Santé, Notre Priorité</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
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
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-100 antialiased">
<!-- Top Navigation Bar -->
<header class="sticky top-0 z-50 w-full bg-white/90 dark:bg-background-dark/90 backdrop-blur-md border-b border-[#e7f3e7] dark:border-gray-800">
<div class="max-w-[1280px] mx-auto px-6 lg:px-20 h-20 flex items-center justify-between">
<div class="flex items-center gap-2">
                <div class="flex items-center gap-1">
                    <a href="#" title="Accueil LOBIKO" class="flex items-center gap-1 group">
                        <span class="size-8 text-primary block">
                            <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor"></path></svg>
                        </span>
                        <h2 class="text-[#0d1b0d] dark:text-white text-xl font-bold tracking-tight ml-1 group-hover:text-primary transition-colors">LOBIKO PHARMACIE <span class="text-primary">- AGRO VETO</span></h2>
                    </a>
                </div>
</div>
<nav class="hidden md:flex items-center gap-10">
<a class="text-[#0d1b0d] dark:text-gray-200 text-sm font-semibold hover:text-primary transition-colors" href="#">Accueil</a>
<a class="text-[#0d1b0d] dark:text-gray-200 text-sm font-semibold hover:text-primary transition-colors" href="#about">À Propos</a>
<a class="text-[#0d1b0d] dark:text-gray-200 text-sm font-semibold hover:text-primary transition-colors" href="#mission">Mission</a>
<a class="text-[#0d1b0d] dark:text-gray-200 text-sm font-semibold hover:text-primary transition-colors" href="#contact">Contact</a>
</nav>
<button class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-all shadow-md active:scale-95 flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]">call</span>
                Appelez-nous
            </button>
</div>
</header>
<main>
<!-- Hero Section -->
<section class="px-6 lg:px-20 py-8">
<div class="max-w-[1280px] mx-auto">
<div class="relative min-h-[560px] flex flex-col items-start justify-end p-8 lg:p-16 rounded-xl overflow-hidden bg-cover bg-center" data-alt="Intérieur d'une pharmacie moderne et lumineuse" style='background-image: linear-gradient(rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.6) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuAIwSf5tPkinwq_n05SuOQ9oN7Bihocfa_yuYSOZrBSeDN1E366yBfozbrfVwtPRkkVmHvHg-3IUp7NjDbJbyHXsXG31zKCqq4TKsIZfE1x__VKfOXjCxLG4-umHvb3pfGn7iehJYxwpgQ0QFIn7C3fd89ipkc-BYT8NsRR35R_Okuo1BSABNf01fEIdBJCnCKRofxKc2gw6XiQL4aVSRErzRsRDsVqUrbLyuQaw7PVcB4Yg5KNJvfvVO0dBXFlwwtlh5oeGzT_D-I");'>
<div class="max-w-[700px] space-y-4">
<h1 class="text-white text-4xl lg:text-6xl font-black leading-tight tracking-tight">
                            Santé & Agriculture, <br/><span class="text-primary">Votre Priorité à Bukavu</span>
</h1>
<p class="text-white/90 text-lg lg:text-xl font-medium max-w-2xl">
                            LOBIKO PHARMACIE - AGRO VETO vous accompagne à Bukavu : médicaments, intrants agro-vétérinaires, conseils experts, innovation digitale et accompagnement personnalisé pour la santé et l’agriculture.
                        </p>
<div class="pt-4">
<a href="#produits" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-base font-bold transition-all shadow-lg flex items-center gap-1" style="width:auto; min-width:0; display:inline-flex;">
    Explorer nos produits
    <span class="material-symbols-outlined" style="font-size:1.1em;">arrow_forward</span>
</a>
</div>
</div>
</div>
</div>
</section>
<!-- About Section (Qui sommes-nous) -->
<section class="px-6 lg:px-20 py-20 bg-white dark:bg-background-dark/30" id="about">
<div class="max-w-[1280px] mx-auto grid lg:grid-cols-2 gap-16 items-center">
<div class="space-y-6">
<div class="inline-block px-3 py-1 bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest rounded-full">
                        LOBIKO PHARMACIE - AGRO VETO | Bukavu
                    </div>
<h2 class="text-3xl lg:text-4xl font-bold text-[#0d1b0d] dark:text-white leading-tight">
                        Votre pharmacie & agro-vétérinaire à Bukavu
                    </h2>
<p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                        LOBIKO PHARMACIE - AGRO VETO est votre partenaire santé et agriculture à Bukavu, sur l’avenue P.E Lumbumba, quartier Nyawera. Nous proposons médicaments, intrants vétérinaires, produits agro, conseils personnalisés et innovation digitale pour la gestion de vos besoins santé et agro-vétérinaires.
                    </p>
<div class="grid grid-cols-2 gap-6 pt-4">
<div class="p-4 border border-[#e7f3e7] dark:border-gray-700 rounded-lg">
<span class="text-3xl font-bold text-primary">5+</span>
<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pharmaciens, agro-vétérinaires & experts</p>
</div>
<div class="p-4 border border-[#e7f3e7] dark:border-gray-700 rounded-lg">
<span class="text-3xl font-bold text-primary">2000+</span>
<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Clients & éleveurs satisfaits à Bukavu</p>
</div>
</div>
</div>
<div class="relative">
<div class="w-full aspect-[4/3] rounded-xl bg-center bg-cover shadow-2xl" data-alt="Équipe de pharmaciens souriants discutant professionnellement" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC3vkps2TQk6Ziyya8VJU5Nd8GP2w6PIceVwD4ylm1DHrJvstUxo-oVwOyQZqNeNo4JMP_uSUlQdv1xnIWsxHI9s2K4wOZbBxcxPfzDsegWr0HbURzXjJuKN9T99GNb3PC8uC073OP-zd6b0BgtCnouL5ujAVh0ZWtCBp0tuSiSNo2SKsF4UX-HnWW89XH7st8iA38OilKt-Ul7z5ZR_J3W5u2YrnywXwtyEf4im1RJikp-Bj8RjK9jloc7CqcjcLqsnzTtlVtWSC4");'>
</div>
<div class="absolute -bottom-6 -left-6 bg-primary p-6 rounded-xl shadow-xl hidden md:block">
<span class="material-symbols-outlined text-white text-4xl">medical_services</span>
<p class="text-white font-bold mt-2">Services d'urgence 24/7</p>
</div>
</div>
</div>
</section>
<!-- Mission & Vision Section -->

<!-- Nos Produits Section -->
<section class="px-6 lg:px-20 py-24 bg-background-light dark:bg-background-dark" id="produits">
    <div class="max-w-[1280px] mx-auto">
        <div class="text-center mb-16 space-y-4">
            <h2 class="text-3xl lg:text-4xl font-bold text-[#0d1b0d] dark:text-white">Nos Produits</h2>
            <div class="w-20 h-1.5 bg-primary mx-auto rounded-full"></div>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">Découvrez notre sélection de produits pharmaceutiques, vétérinaires et agricoles : médicaments, intrants, nutrition animale, phytosanitaires, compléments, hygiène et bien plus pour la santé et l’agriculture à Bukavu.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-8 flex flex-col items-center text-center border border-[#cfe7cf] dark:border-gray-800">
                <span class="material-symbols-outlined text-primary text-5xl mb-4">medication</span>
                <h3 class="font-bold text-xl mb-2">Médicaments & Parapharmacie</h3>
                <p class="text-gray-500 dark:text-gray-400">Antibiotiques, antalgiques, vitamines, soins courants, produits bébé, hygiène, etc.</p>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-8 flex flex-col items-center text-center border border-[#cfe7cf] dark:border-gray-800">
                <span class="material-symbols-outlined text-primary text-5xl mb-4">pets</span>
                <h3 class="font-bold text-xl mb-2">Produits Vétérinaires</h3>
                <p class="text-gray-500 dark:text-gray-400">Vermifuges, vaccins, nutrition animale, soins pour bétail, volailles, chiens, chats…</p>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-8 flex flex-col items-center text-center border border-[#cfe7cf] dark:border-gray-800">
                <span class="material-symbols-outlined text-primary text-5xl mb-4">eco</span>
                <h3 class="font-bold text-xl mb-2">Intrants & Produits Agricoles</h3>
                <p class="text-gray-500 dark:text-gray-400">Semences, engrais, phytosanitaires, outils, conseils pour agriculteurs et éleveurs.</p>
            </div>
        </div>
    </div>
</section>
<section class="px-6 lg:px-20 py-24 bg-background-light dark:bg-background-dark" id="mission">
<div class="max-w-[1280px] mx-auto">
<div class="text-center mb-16 space-y-4">
<h2 class="text-3xl lg:text-4xl font-bold text-[#0d1b0d] dark:text-white">Notre engagement</h2>
<div class="w-20 h-1.5 bg-primary mx-auto rounded-full"></div>
</div>
<div class="grid md:grid-cols-2 gap-8">
<!-- Mission Card -->
<div class="group p-8 lg:p-12 rounded-2xl border border-[#cfe7cf] dark:border-gray-800 bg-white dark:bg-gray-900 hover:shadow-xl transition-all">
<div class="size-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-8 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-[32px]">person_heart</span>
</div>
<h3 class="text-2xl font-bold text-[#0d1b0d] dark:text-white mb-4">Notre Mission</h3>
<p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed">
                            Offrir un accompagnement santé personnalisé, des conseils experts et un accès rapide à vos traitements. LOBIKO s’engage à la transparence, la sécurité et l’innovation pour chaque patient.
                        </p>
</div>
<!-- Vision Card -->
<div class="group p-8 lg:p-12 rounded-2xl border border-[#cfe7cf] dark:border-gray-800 bg-white dark:bg-gray-900 hover:shadow-xl transition-all">
<div class="size-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-8 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-[32px]">visibility</span>
</div>
<h3 class="text-2xl font-bold text-[#0d1b0d] dark:text-white mb-4">Notre Vision</h3>
<p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed">
                            Être la référence pharmaceutique à Lubumbashi, en alliant proximité, innovation digitale et excellence humaine. Nous voulons simplifier la gestion de votre santé au quotidien.
                        </p>
</div>
</div>
</div>
</section>
<!-- Visit Us Section -->
<section class="px-6 lg:px-20 py-24 bg-white dark:bg-background-dark/30 overflow-hidden">
<div class="max-w-[1280px] mx-auto">
<div class="flex flex-col lg:flex-row gap-12">
<div class="lg:w-5/12 space-y-8">
<div>
<h2 class="text-3xl font-bold text-[#0d1b0d] dark:text-white mb-4">Nous Visiter</h2>
<p class="text-gray-600 dark:text-gray-400">Passez nous voir à Bukavu pour des conseils santé, agro et vétérinaires, ou pour récupérer vos produits en toute sérénité.</p>
</div>
<div class="space-y-6">
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary bg-primary/10 p-2 rounded-lg">location_on</span>
<div>
<h4 class="font-bold dark:text-white">Adresse</h4>
<p class="text-gray-500 dark:text-gray-400 text-sm">Avenue P.E Lumbumba, Quartier Nyawera, Bukavu, RDC</p>
</div>
</div>
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary bg-primary/10 p-2 rounded-lg">schedule</span>
<div>
<h4 class="font-bold dark:text-white">Horaires d'ouverture</h4>
<ul class="text-gray-500 dark:text-gray-400 text-sm space-y-1">
<li class="flex justify-between w-64"><span>Lundi - Vendredi</span> <span>08h00 - 20h00</span></li>
<li class="flex justify-between w-64"><span>Samedi</span> <span>09h00 - 18h00</span></li>
<li class="flex justify-between w-64 text-primary font-semibold"><span>Dimanche</span> <span>Fermé (Garde sur appel)</span></li>
</ul>
</div>
</div>
</div>
</div>
<div class="lg:w-7/12">
<div class="w-full h-[400px] rounded-2xl overflow-hidden shadow-inner bg-gray-100 dark:bg-gray-800 flex items-center justify-center relative">
<!-- Placeholder for Map -->
<div class="absolute inset-0 bg-cover bg-center grayscale" data-location="Paris" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBT_pL_fPILgPiFiWIPvPdLRDf1Acx0kK2ujoDRRp_bB4FtGhk9u7O7PoZLbQhPvB3hM1eOXe0RCEWp-8gY8I4nA2ZYJqnN86BSCdd2sXGIh4y17RNfncfkW1qwroXDGAGoBWLQMIbXBm7nuqxJgfj5F4GmDKRKvI4nyApdlYFyqfDzfolJZ5M197BffX7lHBQWkItZEodjw9nzkdPReflmhgrCoUaqvlIOdJwLcgrrxKOJdJEYGE4VbP6dwi5huWeGJVP7Cs_4rEs"); opacity: 0.3;'></div>
<div class="z-10 text-center p-8 bg-white/80 dark:bg-gray-900/80 backdrop-blur rounded-xl border border-gray-200 dark:border-gray-700">
<span class="material-symbols-outlined text-primary text-5xl mb-2">map</span>
<p class="font-bold dark:text-white">Carte Interactive</p>
<p class="text-sm text-gray-500">Cliquez pour voir l'itinéraire complet sur Google Maps</p>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Contact Section -->
<section class="px-6 lg:px-20 py-24 bg-background-light dark:bg-background-dark" id="contact">
<div class="max-w-[960px] mx-auto">
<div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl overflow-hidden border border-[#cfe7cf] dark:border-gray-800">
<div class="grid md:grid-cols-2">
<div class="p-10 lg:p-14 space-y-8 bg-primary text-white">
<h2 class="text-3xl font-bold">Contactez-nous</h2>
<p class="text-white/80">Une question santé, un besoin agro-vétérinaire ? Contactez-nous pour conseils, commandes ou accompagnement personnalisé à Bukavu.</p>
<div class="space-y-4">
<a class="flex items-center gap-4 hover:bg-white/10 p-3 rounded-lg transition-colors" href="tel:+33123456789">
<span class="material-symbols-outlined">phone</span>
<span>Appelez-nous</span>
</a>
<a class="flex items-center gap-4 hover:bg-white/10 p-3 rounded-lg transition-colors" href="tel:+243974051239">
<span class="material-symbols-outlined">call</span>
<span>+243 974 051 239</span>
</a>
<a class="flex items-center gap-4 hover:bg-white/10 p-3 rounded-lg transition-colors" target="_blank" rel="noopener" href="https://wa.me/243974051239?text=Bonjour%20Pharmacie%20LOBIKO%2C%20j%E2%80%99aimerais%20avoir%20des%20informations%20sur%20vos%20services.%20Merci!">
<span class="material-symbols-outlined">chat</span>
<span>WhatsApp LOBIKO Bukavu</span>
</a>
<a class="flex items-center gap-4 hover:bg-white/10 p-3 rounded-lg transition-colors" href="mailto:contact@pharmaciecentrale.fr">
<span class="material-symbols-outlined">mail</span>
<span>contact@lobiko.cd</span>
</a>
</div>
</div>
<div class="p-10 lg:p-14">
<form class="space-y-4" id="contact-whatsapp-form" autocomplete="off">
    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nom complet</label>
        <input name="nom" required class="w-full px-4 py-3 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 focus:ring-primary focus:border-primary" placeholder="Emmanuel Kubiha" type="text"/>
    </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
            <input name="telephone" required class="w-full px-4 py-3 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 focus:ring-primary focus:border-primary" placeholder="+243 000 000 000" type="tel"/>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email</label>
            <input name="email" required class="w-full px-4 py-3 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 focus:ring-primary focus:border-primary" placeholder="emmanuelkubiha@email.com" type="email"/>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Message</label>
            <textarea name="message" required class="w-full px-4 py-3 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 focus:ring-primary focus:border-primary" placeholder="Votre message ici..." rows="4"></textarea>
        </div>
        <button class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-lg transition-all shadow-md" type="submit">
            Envoyer sur WhatsApp
        </button>
</form>
<script>
document.getElementById('contact-whatsapp-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var nom = this.nom.value.trim();
    var telephone = this.telephone.value.trim();
    var email = this.email.value.trim();
    var msg = this.message.value.trim();
    var whatsapp = '243974051239';
    var text = encodeURIComponent('Bonjour LOBIKO PHARMACIE - AGRO VETO Bukavu,\nNom: ' + nom + '\nTéléphone: ' + telephone + '\nEmail: ' + email + '\nMessage: ' + msg);
    var url = 'https://wa.me/' + whatsapp + '?text=' + text;
    window.open(url, '_blank');
});
</script>
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
                <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor"></path></svg>
            </span>
            <span class="font-bold text-lg dark:text-white group-hover:text-primary transition-colors">LOBIKO PHARMACIE <span class="text-primary">- AGRO VETO</span></span>
        </a>
    </div>
    <div class="flex gap-4">
        <a class="size-10 rounded-full border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white hover:border-primary transition-all" href="https://facebook.com/" target="_blank" rel="noopener" title="Facebook">
            <svg class="size-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path></svg>
        </a>
        <a class="size-10 rounded-full border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white hover:border-primary transition-all" href="https://wa.me/243974051239" target="_blank" rel="noopener" title="WhatsApp">
            <svg class="size-5 fill-current" viewBox="0 0 32 32"><path d="M16 3C9.373 3 4 8.373 4 15c0 2.637.86 5.08 2.48 7.13L4 29l7.13-2.48A11.93 11.93 0 0 0 16 27c6.627 0 12-5.373 12-12S22.627 3 16 3zm0 21.5c-2.07 0-4.07-.54-5.8-1.57l-.41-.24-4.13 1.44 1.44-4.13-.24-.41A9.48 9.48 0 0 1 6.5 15c0-5.24 4.26-9.5 9.5-9.5s9.5 4.26 9.5 9.5-4.26 9.5-9.5 9.5zm5.07-7.13c-.28-.14-1.65-.81-1.9-.9-.25-.09-.43-.14-.61.14-.18.28-.7.9-.86 1.08-.16.18-.32.2-.6.07-.28-.14-1.18-.44-2.25-1.4-.83-.74-1.39-1.65-1.55-1.93-.16-.28-.02-.43.12-.57.13-.13.28-.34.42-.51.14-.17.18-.29.28-.48.09-.19.05-.36-.02-.5-.07-.14-.61-1.47-.84-2.01-.22-.54-.45-.47-.61-.48-.16-.01-.36-.01-.56-.01-.19 0-.5.07-.76.36-.26.29-1 1-.97 2.43.03 1.43 1.03 2.81 1.18 3 .15.19 2.03 3.1 4.93 4.22.69.28 1.23.45 1.65.58.69.22 1.32.19 1.81.12.55-.08 1.65-.67 1.89-1.32.23-.65.23-1.2.16-1.32-.07-.12-.25-.19-.53-.33z"></path></svg>
        </a>
        <a class="size-10 rounded-full border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white hover:border-primary transition-all" href="mailto:contact@lobiko.cd" title="Email">
            <svg class="size-5 fill-current" viewBox="0 0 24 24"><path d="M12 13.065l-8-5.065V19a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8l-8 5.065zM21 6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v.217l9 5.7 9-5.7V6z"></path></svg>
        </a>
        <a class="size-10 rounded-full border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white hover:border-primary transition-all" href="https://instagram.com/" target="_blank" rel="noopener" title="Instagram">
            <svg class="size-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path></svg>
        </a>
    </div>
    <div class="text-xs text-gray-400 text-center md:text-right">
    Tous droits réservés à PharmaSuite – <a href="https://www.linkedin.com/in/emmanuel-baraka/" target="_blank" rel="noopener" style="color:#11d411;font-weight:600;text-decoration:underline;">Développeur</a>
    </div>
</div>
        </body>
        </html>