<!DOCTYPE html>
<html lang="{{ $product->landing_page_languages[0] ?? 'fr' }}" class="scroll-smooth" 
      x-data="{ 
          currentLang: '{{ $product->landing_page_languages[0] ?? 'fr' }}', 
          rtlLangs: ['ar', 'he', 'fa', 'ur'],
          i18n: {},
          badgeLabels: {},
          testimonialsData: [],
          pageData: {},
          t(key) { return this.i18n[this.currentLang]?.[key] || this.i18n['en']?.[key] || this.i18n['fr']?.[key] || key; },
          badge(key) { return this.badgeLabels[this.currentLang]?.[key]?.[1] || this.badgeLabels['en']?.[key]?.[1] || this.badgeLabels['fr']?.[key]?.[1] || ''; },
          testimonial(t) { return t[this.currentLang] || t['en'] || t['fr'] || ''; },
          getFeatures() { return this.pageData[this.currentLang]?.features || this.pageData['en']?.features || this.pageData['fr']?.features || []; },
          getTestimonials() { return this.pageData[this.currentLang]?.testimonials || this.pageData['en']?.testimonials || this.pageData['fr']?.testimonials || []; },
          getSteps() { return this.pageData[this.currentLang]?.steps || this.pageData['en']?.steps || this.pageData['fr']?.steps || []; }
      }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($product->description), 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@400;600;700;800;900&family=Bebas+Neue&family=Oswald:wght@400;600;700&family=Montserrat:wght@400;600;700;800;900&family=Playfair+Display:wght@400;600;700;800;900&family=Roboto:wght@400;500;700;900&family=Poppins:wght@400;600;700;800;900&family=Anton&family=Raleway:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if($store->facebook_pixel_enabled && $store->facebook_pixel_id)
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $store->facebook_pixel_id }}');
        fbq('track', 'PageView');
        fbq('track', 'ViewContent', {
            content_name: '{{ addslashes($product->name) }}',
            content_ids: ['{{ $product->id }}'],
            content_type: 'product',
            value: {{ $product->price }},
            currency: '{{ $product->landing_page_currency ?? 'MAD' }}'
        });
    </script>
    @endif

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f0; }
        .rtl { direction: rtl; font-family: 'Cairo', sans-serif; }
        .font-display { font-family: 'Bebas Neue', 'Cairo', sans-serif; letter-spacing: 0.02em; }
        .stripe-bg {
            background-image: repeating-linear-gradient(
                45deg,
                rgba(255,255,255,0.08) 0,
                rgba(255,255,255,0.08) 12px,
                transparent 12px,
                transparent 24px
            );
        }
        @keyframes pulse-scale { 0%,100% { transform: scale(1); } 50% { transform: scale(1.04); } }
        .animate-pulse-scale { animation: pulse-scale 1.6s ease-in-out infinite; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 20s linear infinite; }
    </style>
</head>
@php
    $td = $product->theme_data ?? [];
    $shortDescription = $td['short_description'] ?? '';
    $promoBadge = $td['promo_badge'] ?? '-50% OFF TODAY';
    $promoBadgeColor = $td['promo_badge_color'] ?? 'red';
    $ctaText = $td['cta_text'] ?? 'ORDER NOW';
    $ctaColor = $td['cta_color'] ?? 'orange';
    $titleColor = $td['title_color'] ?? '#000000';
    $titleFont = $td['title_font'] ?? 'bebas';
    $statsCustomers = $td['stats_customers'] ?? '325';
    $statsRating = $td['stats_rating'] ?? '4.8';
    $statsReviews = $td['stats_reviews'] ?? '127';
    $staticFeatures = $td['features'] ?? [];
    $badges = $td['badges'] ?? [];
    $customTrustBadges = $td['trust_badges'] ?? [];
    $images = $product->all_images ?? [];
    
    $fontFamilyMap = [
        'bebas' => "'Bebas Neue', sans-serif",
        'inter' => "'Inter', sans-serif",
        'cairo' => "'Cairo', sans-serif",
        'oswald' => "'Oswald', sans-serif",
        'montserrat' => "'Montserrat', sans-serif",
        'playfair' => "'Playfair Display', serif",
        'roboto' => "'Roboto', sans-serif",
        'poppins' => "'Poppins', sans-serif",
        'anton' => "'Anton', sans-serif",
        'raleway' => "'Raleway', sans-serif",
    ];
    $titleFontFamily = $fontFamilyMap[$titleFont] ?? $fontFamilyMap['bebas'];
    
    // Build translations from unified column or legacy columns
    $translations = $product->landing_page_translations ?? [];
    if (empty($translations)) {
        if (!empty($product->landing_page_fr)) $translations['fr'] = $product->landing_page_fr;
        if (!empty($product->landing_page_en)) $translations['en'] = $product->landing_page_en;
        if (!empty($product->landing_page_ar)) $translations['ar'] = $product->landing_page_ar;
    }
    
    // Build pageData for JavaScript - this contains AI-generated content
    $pageData = [];
    foreach ($translations as $lang => $data) {
        if (is_array($data)) {
            $pageData[$lang] = $data;
        }
    }
    
    // Ensure enabled languages have data (fallback to first available)
    $enabledLangs = $product->landing_page_languages ?? ['fr'];
    $availableLangs = array_keys($pageData);
    $fallbackLang = !empty($availableLangs) ? $availableLangs[0] : 'fr';
    
    foreach ($enabledLangs as $lang) {
        if (!isset($pageData[$lang]) || empty($pageData[$lang]['features'])) {
            $pageData[$lang] = $pageData[$fallbackLang] ?? [];
        }
    }
    
    // Check if we have AI-generated features (prefer over static)
    $hasAiFeatures = false;
    foreach ($pageData as $langData) {
        if (!empty($langData['features'])) {
            $hasAiFeatures = true;
            break;
        }
    }

    $heroBgMap = [
        'red' => 'from-red-500 via-red-600 to-red-700',
        'orange' => 'from-orange-500 via-orange-600 to-amber-600',
        'green' => 'from-emerald-500 via-green-600 to-green-700',
        'blue' => 'from-blue-500 via-blue-600 to-indigo-700',
        'purple' => 'from-purple-500 via-purple-600 to-fuchsia-700',
    ];
    $ctaGradientMap = [
        'orange' => 'from-orange-500 to-red-600',
        'green' => 'from-green-500 to-emerald-600',
        'red' => 'from-red-500 to-rose-600',
        'blue' => 'from-blue-500 to-indigo-600',
    ];
    $heroBg = $heroBgMap[$promoBadgeColor] ?? $heroBgMap['orange'];
    $ctaBg = $ctaGradientMap[$ctaColor] ?? $ctaGradientMap['orange'];

    $featureIconMap = [
        'steam' => '🔥', 'clean' => '✨', 'fast' => '⚡', 'eco' => '🌿',
        'power' => '💪', 'safe' => '🛡️', 'timer' => '⏱️', 'warranty' => '📋',
    ];

    $badgeLabels = [
        'fr' => [
            'free_shipping' => ['🚚', 'Livraison gratuite'],
            'money_back' => ['💰', 'Satisfait ou remboursé'],
            'secure_payment' => ['🔒', 'Paiement sécurisé'],
            'warranty' => ['✅', 'Garantie 1 an'],
            'cod' => ['💵', 'Paiement à la livraison'],
            'fast_delivery' => ['⚡', 'Livraison 24-48h'],
        ],
        'en' => [
            'free_shipping' => ['🚚', 'Free Shipping'],
            'money_back' => ['💰', 'Money Back Guarantee'],
            'secure_payment' => ['🔒', 'Secure Payment'],
            'warranty' => ['✅', '1 Year Warranty'],
            'cod' => ['💵', 'Cash On Delivery'],
            'fast_delivery' => ['⚡', 'Fast Delivery 24-48h'],
        ],
        'ar' => [
            'free_shipping' => ['🚚', 'شحن مجاني'],
            'money_back' => ['💰', 'ضمان استرداد الأموال'],
            'secure_payment' => ['🔒', 'دفع آمن'],
            'warranty' => ['✅', 'ضمان سنة'],
            'cod' => ['💵', 'الدفع عند الاستلام'],
            'fast_delivery' => ['⚡', 'توصيل 24-48 ساعة'],
        ],
        'sw' => [
            'free_shipping' => ['🚚', 'Usafirishaji bure'],
            'money_back' => ['💰', 'Dhamana ya kurudishiwa pesa'],
            'secure_payment' => ['🔒', 'Malipo salama'],
            'warranty' => ['✅', 'Dhamana ya mwaka 1'],
            'cod' => ['💵', 'Lipa wakati wa utoaji'],
            'fast_delivery' => ['⚡', 'Utoaji wa haraka 24-48h'],
        ],
    ];

    $i18n = [
        'fr' => [
            'order_now' => 'COMMANDEZ MAINTENANT', 'name' => 'Nom complet', 'phone' => 'Téléphone',
            'city' => 'Ville', 'address' => 'Adresse', 'note' => 'Note',
            'send_order' => 'Envoyer la commande', 'cod' => 'PAIEMENT À LA LIVRAISON',
            'limited_stock' => 'STOCK LIMITÉ', 'only_today' => 'Offre valable aujourd\'hui seulement !',
            'customers' => 'Clients', 'rating' => 'Note', 'reviews' => 'Avis',
            'why_choose' => 'Pourquoi choisir ce produit ?',
            'how_to_order' => 'COMMENT COMMANDER',
            'step1_t' => 'Remplissez le formulaire', 'step1_d' => 'Vos informations en toute sécurité',
            'step2_t' => 'Nous vous appelons', 'step2_d' => 'Pour confirmer votre commande',
            'step3_t' => 'Livraison à domicile', 'step3_d' => 'Payez à la réception',
            'testimonials' => 'Témoignages de nos clients',
            'share' => 'Partagez :', 'guarantee' => 'Garantie 100% Satisfait ou Remboursé',
        ],
        'en' => [
            'order_now' => 'ORDER NOW', 'name' => 'Full Name', 'phone' => 'Phone',
            'city' => 'City', 'address' => 'Address', 'note' => 'Note',
            'send_order' => 'Send Order', 'cod' => 'CASH ON DELIVERY',
            'limited_stock' => 'LIMITED STOCK', 'only_today' => 'Offer only valid today!',
            'customers' => 'Customers', 'rating' => 'Rating', 'reviews' => 'Reviews',
            'why_choose' => 'Why choose this product?',
            'how_to_order' => 'HOW TO ORDER',
            'step1_t' => 'Fill the form', 'step1_d' => 'Your info kept 100% safe',
            'step2_t' => 'We call you', 'step2_d' => 'To confirm your order',
            'step3_t' => 'Home delivery', 'step3_d' => 'Pay on arrival',
            'testimonials' => 'What our customers say',
            'share' => 'Share:', 'guarantee' => '100% Satisfied or Refunded',
        ],
        'ar' => [
            'order_now' => 'اطلب الآن', 'name' => 'الاسم الكامل', 'phone' => 'رقم الهاتف',
            'city' => 'المدينة', 'address' => 'العنوان', 'note' => 'ملاحظة',
            'send_order' => 'أرسل طلبك', 'cod' => 'الدفع عند الاستلام',
            'limited_stock' => 'الكمية محدودة', 'only_today' => 'العرض ساري اليوم فقط !',
            'customers' => 'عميل', 'rating' => 'التقييم', 'reviews' => 'مراجعة',
            'why_choose' => 'لماذا تختار هذا المنتج ؟',
            'how_to_order' => 'كيف تطلب المنتج',
            'step1_t' => 'املأ النموذج', 'step1_d' => 'بياناتك آمنة 100%',
            'step2_t' => 'نتصل بك', 'step2_d' => 'لتأكيد طلبك',
            'step3_t' => 'التوصيل إلى البيت', 'step3_d' => 'ادفع عند الاستلام',
            'testimonials' => 'آراء عملائنا',
            'share' => 'شارك :', 'guarantee' => '100% راض أو تسترد أموالك',
        ],
        'es' => ['order_now' => 'ORDENAR AHORA', 'name' => 'Nombre completo', 'phone' => 'Teléfono', 'city' => 'Ciudad', 'address' => 'Dirección', 'note' => 'Nota', 'send_order' => 'Enviar pedido', 'cod' => 'PAGO CONTRA ENTREGA', 'limited_stock' => 'STOCK LIMITADO', 'only_today' => '¡Oferta válida solo hoy!', 'customers' => 'Clientes', 'rating' => 'Calificación', 'reviews' => 'Reseñas', 'why_choose' => '¿Por qué elegir este producto?', 'how_to_order' => 'CÓMO ORDENAR', 'step1_t' => 'Rellena el formulario', 'step1_d' => 'Tu información 100% segura', 'step2_t' => 'Te llamamos', 'step2_d' => 'Para confirmar tu pedido', 'step3_t' => 'Entrega a domicilio', 'step3_d' => 'Paga al recibir', 'testimonials' => 'Lo que dicen nuestros clientes', 'share' => 'Compartir:', 'guarantee' => '100% Satisfecho o Reembolsado'],
        'de' => ['order_now' => 'JETZT BESTELLEN', 'name' => 'Vollständiger Name', 'phone' => 'Telefon', 'city' => 'Stadt', 'address' => 'Adresse', 'note' => 'Notiz', 'send_order' => 'Bestellung senden', 'cod' => 'NACHNAHME', 'limited_stock' => 'BEGRENZTER VORRAT', 'only_today' => 'Angebot nur heute gültig!', 'customers' => 'Kunden', 'rating' => 'Bewertung', 'reviews' => 'Rezensionen', 'why_choose' => 'Warum dieses Produkt wählen?', 'how_to_order' => 'SO BESTELLEN', 'step1_t' => 'Formular ausfüllen', 'step1_d' => 'Deine Daten sind 100% sicher', 'step2_t' => 'Wir rufen dich an', 'step2_d' => 'Um deine Bestellung zu bestätigen', 'step3_t' => 'Lieferung nach Hause', 'step3_d' => 'Bei Erhalt zahlen', 'testimonials' => 'Was unsere Kunden sagen', 'share' => 'Teilen:', 'guarantee' => '100% Zufrieden oder Geld zurück'],
        'it' => ['order_now' => 'ORDINA ORA', 'name' => 'Nome completo', 'phone' => 'Telefono', 'city' => 'Città', 'address' => 'Indirizzo', 'note' => 'Nota', 'send_order' => 'Invia ordine', 'cod' => 'PAGAMENTO ALLA CONSEGNA', 'limited_stock' => 'SCORTE LIMITATE', 'only_today' => 'Offerta valida solo oggi!', 'customers' => 'Clienti', 'rating' => 'Valutazione', 'reviews' => 'Recensioni', 'why_choose' => 'Perché scegliere questo prodotto?', 'how_to_order' => 'COME ORDINARE', 'step1_t' => 'Compila il modulo', 'step1_d' => 'I tuoi dati sono 100% sicuri', 'step2_t' => 'Ti chiamiamo', 'step2_d' => 'Per confermare il tuo ordine', 'step3_t' => 'Consegna a domicilio', 'step3_d' => 'Paga alla consegna', 'testimonials' => 'Cosa dicono i nostri clienti', 'share' => 'Condividi:', 'guarantee' => '100% Soddisfatto o Rimborsato'],
        'pt' => ['order_now' => 'PEDIR AGORA', 'name' => 'Nome completo', 'phone' => 'Telefone', 'city' => 'Cidade', 'address' => 'Endereço', 'note' => 'Nota', 'send_order' => 'Enviar pedido', 'cod' => 'PAGAMENTO NA ENTREGA', 'limited_stock' => 'ESTOQUE LIMITADO', 'only_today' => 'Oferta válida apenas hoje!', 'customers' => 'Clientes', 'rating' => 'Avaliação', 'reviews' => 'Avaliações', 'why_choose' => 'Por que escolher este produto?', 'how_to_order' => 'COMO PEDIR', 'step1_t' => 'Preencha o formulário', 'step1_d' => 'Suas informações 100% seguras', 'step2_t' => 'Ligamos para você', 'step2_d' => 'Para confirmar seu pedido', 'step3_t' => 'Entrega em casa', 'step3_d' => 'Pague na chegada', 'testimonials' => 'O que nossos clientes dizem', 'share' => 'Compartilhar:', 'guarantee' => '100% Satisfeito ou Reembolsado'],
        'ru' => ['order_now' => 'ЗАКАЗАТЬ СЕЙЧАС', 'name' => 'Полное имя', 'phone' => 'Телефон', 'city' => 'Город', 'address' => 'Адрес', 'note' => 'Примечание', 'send_order' => 'Отправить заказ', 'cod' => 'ОПЛАТА ПРИ ДОСТАВКЕ', 'limited_stock' => 'ОГРАНИЧЕННЫЙ ЗАПАС', 'only_today' => 'Предложение действует только сегодня!', 'customers' => 'Клиенты', 'rating' => 'Рейтинг', 'reviews' => 'Отзывы', 'why_choose' => 'Почему выбрать этот продукт?', 'how_to_order' => 'КАК ЗАКАЗАТЬ', 'step1_t' => 'Заполните форму', 'step1_d' => 'Ваша информация 100% в безопасности', 'step2_t' => 'Мы вам перезвоним', 'step2_d' => 'Чтобы подтвердить ваш заказ', 'step3_t' => 'Доставка на дом', 'step3_d' => 'Оплата при получении', 'testimonials' => 'Что говорят наши клиенты', 'share' => 'Поделиться:', 'guarantee' => '100% Удовлетворен или возврат денег'],
        'zh' => ['order_now' => '立即订购', 'name' => '全名', 'phone' => '电话', 'city' => '城市', 'address' => '地址', 'note' => '备注', 'send_order' => '发送订单', 'cod' => '货到付款', 'limited_stock' => '库存有限', 'only_today' => '优惠仅限今日有效!', 'customers' => '客户', 'rating' => '评分', 'reviews' => '评论', 'why_choose' => '为什么选择这款产品?', 'how_to_order' => '如何订购', 'step1_t' => '填写表格', 'step1_d' => '您的信息100%安全', 'step2_t' => '我们给您打电话', 'step2_d' => '以确认您的订单', 'step3_t' => '送货上门', 'step3_d' => '到货付款', 'testimonials' => '客户评价', 'share' => '分享:', 'guarantee' => '100%满意或退款'],
        'ja' => ['order_now' => '今すぐ注文', 'name' => 'フルネーム', 'phone' => '電話', 'city' => '都市', 'address' => '住所', 'note' => 'メモ', 'send_order' => '注文を送信', 'cod' => '代金引換', 'limited_stock' => '在庫限定', 'only_today' => '本日限りのオファー!', 'customers' => '顧客', 'rating' => '評価', 'reviews' => 'レビュー', 'why_choose' => 'なぜこの製品を選ぶのか?', 'how_to_order' => '注文方法', 'step1_t' => 'フォームに記入', 'step1_d' => '個人情報は100%安全', 'step2_t' => 'お電話します', 'step2_d' => 'ご注文を確認するために', 'step3_t' => '自宅配達', 'step3_d' => '到着時にお支払い', 'testimonials' => 'お客様の声', 'share' => '共有:', 'guarantee' => '100%満足または返金'],
        'ko' => ['order_now' => '지금 주문', 'name' => '전체 이름', 'phone' => '전화', 'city' => '도시', 'address' => '주소', 'note' => '메모', 'send_order' => '주문 보내기', 'cod' => '대금 상환', 'limited_stock' => '한정 재고', 'only_today' => '오늘만 유효한 제안!', 'customers' => '고객', 'rating' => '평점', 'reviews' => '리뷰', 'why_choose' => '왜 이 제품을 선택해야 합니까?', 'how_to_order' => '주문 방법', 'step1_t' => '양식 작성', 'step1_d' => '당신의 정보는 100% 안전', 'step2_t' => '전화드립니다', 'step2_d' => '주문 확인을 위해', 'step3_t' => '자택 배달', 'step3_d' => '도착시 지불', 'testimonials' => '고객 후기', 'share' => '공유:', 'guarantee' => '100% 만족 또는 환불'],
        'tr' => ['order_now' => 'ŞİMDİ SİPARİŞ VER', 'name' => 'Ad Soyad', 'phone' => 'Telefon', 'city' => 'Şehir', 'address' => 'Adres', 'note' => 'Not', 'send_order' => 'Sipariş Gönder', 'cod' => 'KAPIDA ÖDEME', 'limited_stock' => 'SINIRLI STOK', 'only_today' => 'Teklif yalnızca bugün geçerli!', 'customers' => 'Müşteriler', 'rating' => 'Değerlendirme', 'reviews' => 'Yorumlar', 'why_choose' => 'Bu ürünü neden seçmelisiniz?', 'how_to_order' => 'NASIL SİPARİŞ VERİLİR', 'step1_t' => 'Formu doldurun', 'step1_d' => 'Bilgileriniz %100 güvende', 'step2_t' => 'Sizi arıyoruz', 'step2_d' => 'Siparişinizi onaylamak için', 'step3_t' => 'Eve teslim', 'step3_d' => 'Teslimatta ödeyin', 'testimonials' => 'Müşterilerimiz ne diyor', 'share' => 'Paylaş:', 'guarantee' => '%100 Memnun ya da Para İadesi'],
        'nl' => ['order_now' => 'NU BESTELLEN', 'name' => 'Volledige naam', 'phone' => 'Telefoon', 'city' => 'Stad', 'address' => 'Adres', 'note' => 'Notitie', 'send_order' => 'Bestelling versturen', 'cod' => 'BETALEN BIJ LEVERING', 'limited_stock' => 'BEPERKTE VOORRAAD', 'only_today' => 'Aanbieding alleen vandaag geldig!', 'customers' => 'Klanten', 'rating' => 'Beoordeling', 'reviews' => 'Beoordelingen', 'why_choose' => 'Waarom dit product kiezen?', 'how_to_order' => 'HOE TE BESTELLEN', 'step1_t' => 'Vul het formulier in', 'step1_d' => 'Je gegevens zijn 100% veilig', 'step2_t' => 'We bellen je', 'step2_d' => 'Om je bestelling te bevestigen', 'step3_t' => 'Thuisbezorging', 'step3_d' => 'Betaal bij aankomst', 'testimonials' => 'Wat onze klanten zeggen', 'share' => 'Delen:', 'guarantee' => '100% Tevreden of Terugbetaling'],
        'pl' => ['order_now' => 'ZAMÓW TERAZ', 'name' => 'Imię i nazwisko', 'phone' => 'Telefon', 'city' => 'Miasto', 'address' => 'Adres', 'note' => 'Notatka', 'send_order' => 'Wyślij zamówienie', 'cod' => 'PŁATNOŚĆ PRZY ODBIORZE', 'limited_stock' => 'OGRANICZONY ZAPAS', 'only_today' => 'Oferta ważna tylko dzisiaj!', 'customers' => 'Klienci', 'rating' => 'Ocena', 'reviews' => 'Opinie', 'why_choose' => 'Dlaczego wybrać ten produkt?', 'how_to_order' => 'JAK ZAMÓWIĆ', 'step1_t' => 'Wypełnij formularz', 'step1_d' => 'Twoje dane są w 100% bezpieczne', 'step2_t' => 'Oddzwonimy', 'step2_d' => 'Aby potwierdzić zamówienie', 'step3_t' => 'Dostawa do domu', 'step3_d' => 'Zapłać przy odbiorze', 'testimonials' => 'Co mówią nasi klienci', 'share' => 'Udostępnij:', 'guarantee' => '100% satysfakcji lub zwrot pieniędzy'],
        'hi' => ['order_now' => 'अभी ऑर्डर करें', 'name' => 'पूरा नाम', 'phone' => 'फ़ोन', 'city' => 'शहर', 'address' => 'पता', 'note' => 'नोट', 'send_order' => 'ऑर्डर भेजें', 'cod' => 'डिलीवरी पर कैश', 'limited_stock' => 'सीमित स्टॉक', 'only_today' => 'ऑफर केवल आज के लिए मान्य!', 'customers' => 'ग्राहक', 'rating' => 'रेटिंग', 'reviews' => 'समीक्षाएं', 'why_choose' => 'यह उत्पाद क्यों चुनें?', 'how_to_order' => 'ऑर्डर कैसे करें', 'step1_t' => 'फॉर्म भरें', 'step1_d' => 'आपकी जानकारी 100% सुरक्षित', 'step2_t' => 'हम आपको कॉल करेंगे', 'step2_d' => 'आपके आदेश की पुष्टि के लिए', 'step3_t' => 'होम डिलीवरी', 'step3_d' => 'आगमन पर भुगतान करें', 'testimonials' => 'हमारे ग्राहक क्या कहते हैं', 'share' => 'साझा करें:', 'guarantee' => '100% संतुष्ट या वापसी'],
        'th' => ['order_now' => 'สั่งซื้อเลย', 'name' => 'ชื่อเต็ม', 'phone' => 'โทรศัพท์', 'city' => 'เมือง', 'address' => 'ที่อยู่', 'note' => 'หมายเหตุ', 'send_order' => 'ส่งคำสั่งซื้อ', 'cod' => 'เก็บเงินปลายทาง', 'limited_stock' => 'สต็อกจำกัด', 'only_today' => 'ข้อเสนอนี้ใช้ได้เฉพาะวันนี้!', 'customers' => 'ลูกค้า', 'rating' => 'คะแนน', 'reviews' => 'รีวิว', 'why_choose' => 'ทำไมต้องเลือกผลิตภัณฑ์นี้?', 'how_to_order' => 'วิธีการสั่งซื้อ', 'step1_t' => 'กรอกแบบฟอร์ม', 'step1_d' => 'ข้อมูลของคุณปลอดภัย 100%', 'step2_t' => 'เราจะโทรหาคุณ', 'step2_d' => 'เพื่อยืนยันคำสั่งซื้อของคุณ', 'step3_t' => 'จัดส่งถึงบ้าน', 'step3_d' => 'จ่ายเมื่อได้รับสินค้า', 'testimonials' => 'สิ่งที่ลูกค้าของเราพูด', 'share' => 'แชร์:', 'guarantee' => '100% พึงพอใจหรือคืนเงิน'],
        'vi' => ['order_now' => 'ĐẶT HÀNG NGAY', 'name' => 'Họ và tên', 'phone' => 'Điện thoại', 'city' => 'Thành phố', 'address' => 'Địa chỉ', 'note' => 'Ghi chú', 'send_order' => 'Gửi đơn hàng', 'cod' => 'THANH TOÁN KHI NHẬN HÀNG', 'limited_stock' => 'TỒN KHO HẠN CHẾ', 'only_today' => 'Ưu đãi chỉ có hiệu lực hôm nay!', 'customers' => 'Khách hàng', 'rating' => 'Đánh giá', 'reviews' => 'Nhận xét', 'why_choose' => 'Tại sao chọn sản phẩm này?', 'how_to_order' => 'CÁCH ĐẶT HÀNG', 'step1_t' => 'Điền vào biểu mẫu', 'step1_d' => 'Thông tin của bạn an toàn 100%', 'step2_t' => 'Chúng tôi gọi cho bạn', 'step2_d' => 'Để xác nhận đơn hàng của bạn', 'step3_t' => 'Giao hàng tận nhà', 'step3_d' => 'Thanh toán khi nhận hàng', 'testimonials' => 'Khách hàng của chúng tôi nói gì', 'share' => 'Chia sẻ:', 'guarantee' => '100% Hài lòng hoặc Hoàn tiền'],
        'id' => ['order_now' => 'PESAN SEKARANG', 'name' => 'Nama lengkap', 'phone' => 'Telepon', 'city' => 'Kota', 'address' => 'Alamat', 'note' => 'Catatan', 'send_order' => 'Kirim pesanan', 'cod' => 'BAYAR DI TEMPAT', 'limited_stock' => 'STOK TERBATAS', 'only_today' => 'Penawaran hanya berlaku hari ini!', 'customers' => 'Pelanggan', 'rating' => 'Peringkat', 'reviews' => 'Ulasan', 'why_choose' => 'Mengapa memilih produk ini?', 'how_to_order' => 'CARA MEMESAN', 'step1_t' => 'Isi formulir', 'step1_d' => 'Informasi Anda aman 100%', 'step2_t' => 'Kami menelepon Anda', 'step2_d' => 'Untuk mengonfirmasi pesanan Anda', 'step3_t' => 'Pengiriman rumah', 'step3_d' => 'Bayar saat tiba', 'testimonials' => 'Apa kata pelanggan kami', 'share' => 'Bagikan:', 'guarantee' => '100% Puas atau Uang Kembali'],
        'ms' => ['order_now' => 'PESAN SEKARANG', 'name' => 'Nama penuh', 'phone' => 'Telefon', 'city' => 'Bandar', 'address' => 'Alamat', 'note' => 'Nota', 'send_order' => 'Hantar pesanan', 'cod' => 'BAYAR SEMASA TERIMA', 'limited_stock' => 'STOK TERHAD', 'only_today' => 'Tawaran sah hari ini sahaja!', 'customers' => 'Pelanggan', 'rating' => 'Penilaian', 'reviews' => 'Ulasan', 'why_choose' => 'Mengapa memilih produk ini?', 'how_to_order' => 'CARA MEMESAN', 'step1_t' => 'Isi borang', 'step1_d' => 'Maklumat anda 100% selamat', 'step2_t' => 'Kami panggil anda', 'step2_d' => 'Untuk mengesahkan pesanan anda', 'step3_t' => 'Penghantaran rumah', 'step3_d' => 'Bayar semasa tiba', 'testimonials' => 'Apa kata pelanggan kami', 'share' => 'Kongsi:', 'guarantee' => '100% Puas atau Wang Dikembalikan'],
        'he' => ['order_now' => 'הזמן עכשיו', 'name' => 'שם מלא', 'phone' => 'טלפון', 'city' => 'עיר', 'address' => 'כתובת', 'note' => 'הערה', 'send_order' => 'שלח הזמנה', 'cod' => 'תשלום במזומן במשלוח', 'limited_stock' => 'מלאי מוגבל', 'only_today' => 'הצעה בתוקף היום בלבד!', 'customers' => 'לקוחות', 'rating' => 'דירוג', 'reviews' => 'ביקורות', 'why_choose' => 'למה לבחור במוצר זה?', 'how_to_order' => 'איך להזמין', 'step1_t' => 'מלא את הטופס', 'step1_d' => 'המידע שלך מאובטח 100%', 'step2_t' => 'אנו מתקשרים אליך', 'step2_d' => 'כדי לאשר את הזמנתך', 'step3_t' => 'משלוח עד הבית', 'step3_d' => 'שלם בהגעה', 'testimonials' => 'מה הלקוחות שלנו אומרים', 'share' => 'שתף:', 'guarantee' => '100% מרוצה או החזר כספי'],
        'el' => ['order_now' => 'ΠΑΡΑΓΓΕΙΛΤΕ ΤΩΡΑ', 'name' => 'Πλήρες όνομα', 'phone' => 'Τηλέφωνο', 'city' => 'Πόλη', 'address' => 'Διεύθυνση', 'note' => 'Σημείωση', 'send_order' => 'Αποστολή παραγγελίας', 'cod' => 'ΑΝΤΙΚΑΤΑΒΟΛΗ', 'limited_stock' => 'ΠΕΡΙΟΡΙΣΜΕΝΟ ΑΠΟΘΕΜΑ', 'only_today' => 'Η προσφορά ισχύει μόνο σήμερα!', 'customers' => 'Πελάτες', 'rating' => 'Βαθμολογία', 'reviews' => 'Κριτικές', 'why_choose' => 'Γιατί να επιλέξετε αυτό το προϊόν;', 'how_to_order' => 'ΠΩΣ ΝΑ ΠΑΡΑΓΓΕΙΛΕΤΕ', 'step1_t' => 'Συμπληρώστε τη φόρμα', 'step1_d' => 'Τα στοιχεία σας είναι 100% ασφαλή', 'step2_t' => 'Σας τηλεφωνούμε', 'step2_d' => 'Για να επιβεβαιώσουμε την παραγγελία σας', 'step3_t' => 'Παράδοση στο σπίτι', 'step3_d' => 'Πληρώστε κατά την άφιξη', 'testimonials' => 'Τι λένε οι πελάτες μας', 'share' => 'Κοινοποίηση:', 'guarantee' => '100% Ικανοποιημένοι ή Επιστροφή Χρημάτων'],
        'cs' => ['order_now' => 'OBJEDNAT NYNÍ', 'name' => 'Celé jméno', 'phone' => 'Telefon', 'city' => 'Město', 'address' => 'Adresa', 'note' => 'Poznámka', 'send_order' => 'Odeslat objednávku', 'cod' => 'PLATBA NA DOBÍRKU', 'limited_stock' => 'OMEZENÉ ZÁSOBY', 'only_today' => 'Nabídka platí pouze dnes!', 'customers' => 'Zákazníci', 'rating' => 'Hodnocení', 'reviews' => 'Recenze', 'why_choose' => 'Proč vybrat tento produkt?', 'how_to_order' => 'JAK OBJEDNAT', 'step1_t' => 'Vyplňte formulář', 'step1_d' => 'Vaše informace jsou 100% v bezpečí', 'step2_t' => 'Zavoláme vám', 'step2_d' => 'Pro potvrzení objednávky', 'step3_t' => 'Doručení domů', 'step3_d' => 'Platba při doručení', 'testimonials' => 'Co říkají naši zákazníci', 'share' => 'Sdílet:', 'guarantee' => '100% Spokojenost nebo Vrácení Peněz'],
        'sv' => ['order_now' => 'BESTÄLL NU', 'name' => 'Fullständigt namn', 'phone' => 'Telefon', 'city' => 'Stad', 'address' => 'Adress', 'note' => 'Anteckning', 'send_order' => 'Skicka beställning', 'cod' => 'POSTFÖRSKOTT', 'limited_stock' => 'BEGRÄNSAT LAGER', 'only_today' => 'Erbjudandet gäller endast idag!', 'customers' => 'Kunder', 'rating' => 'Betyg', 'reviews' => 'Recensioner', 'why_choose' => 'Varför välja denna produkt?', 'how_to_order' => 'HUR MAN BESTÄLLER', 'step1_t' => 'Fyll i formuläret', 'step1_d' => 'Din info är 100% säker', 'step2_t' => 'Vi ringer dig', 'step2_d' => 'För att bekräfta din beställning', 'step3_t' => 'Hemleverans', 'step3_d' => 'Betala vid ankomst', 'testimonials' => 'Vad våra kunder säger', 'share' => 'Dela:', 'guarantee' => '100% Nöjd eller Pengarna Tillbaka'],
        'no' => ['order_now' => 'BESTILL NÅ', 'name' => 'Fullt navn', 'phone' => 'Telefon', 'city' => 'By', 'address' => 'Adresse', 'note' => 'Notat', 'send_order' => 'Send bestilling', 'cod' => 'POSTOPPKRAV', 'limited_stock' => 'BEGRENSET LAGER', 'only_today' => 'Tilbud gjelder kun i dag!', 'customers' => 'Kunder', 'rating' => 'Vurdering', 'reviews' => 'Anmeldelser', 'why_choose' => 'Hvorfor velge dette produktet?', 'how_to_order' => 'HVORDAN BESTILLE', 'step1_t' => 'Fyll ut skjemaet', 'step1_d' => 'Informasjonen din er 100% sikker', 'step2_t' => 'Vi ringer deg', 'step2_d' => 'For å bekrefte bestillingen din', 'step3_t' => 'Hjemlevering', 'step3_d' => 'Betal ved ankomst', 'testimonials' => 'Hva kundene våre sier', 'share' => 'Del:', 'guarantee' => '100% Fornøyd eller Pengene Tilbake'],
        'da' => ['order_now' => 'BESTIL NU', 'name' => 'Fulde navn', 'phone' => 'Telefon', 'city' => 'By', 'address' => 'Adresse', 'note' => 'Note', 'send_order' => 'Send bestilling', 'cod' => 'BETALING VED LEVERING', 'limited_stock' => 'BEGRÆNSET LAGER', 'only_today' => 'Tilbud gælder kun i dag!', 'customers' => 'Kunder', 'rating' => 'Bedømmelse', 'reviews' => 'Anmeldelser', 'why_choose' => 'Hvorfor vælge dette produkt?', 'how_to_order' => 'HVORDAN BESTILLER', 'step1_t' => 'Udfyld formularen', 'step1_d' => 'Dine oplysninger er 100% sikre', 'step2_t' => 'Vi ringer til dig', 'step2_d' => 'For at bekræfte din bestilling', 'step3_t' => 'Hjemmelevering', 'step3_d' => 'Betal ved ankomst', 'testimonials' => 'Hvad vores kunder siger', 'share' => 'Del:', 'guarantee' => '100% Tilfredshed eller Pengene Tilbage'],
        'fi' => ['order_now' => 'TILAA NYT', 'name' => 'Koko nimi', 'phone' => 'Puhelin', 'city' => 'Kaupunki', 'address' => 'Osoite', 'note' => 'Huomautus', 'send_order' => 'Lähetä tilaus', 'cod' => 'KÄTEISMAKSU TOIMITUKSESSA', 'limited_stock' => 'RAJOITETTU VARASTO', 'only_today' => 'Tarjous voimassa vain tänään!', 'customers' => 'Asiakkaat', 'rating' => 'Arvio', 'reviews' => 'Arvostelut', 'why_choose' => 'Miksi valita tämä tuote?', 'how_to_order' => 'MITEN TILAAT', 'step1_t' => 'Täytä lomake', 'step1_d' => 'Tietosi ovat 100% turvassa', 'step2_t' => 'Soitamme sinulle', 'step2_d' => 'Vahvistaaksemme tilauksesi', 'step3_t' => 'Kotiinkuljetus', 'step3_d' => 'Maksu saapuessa', 'testimonials' => 'Mitä asiakkaamme sanovat', 'share' => 'Jaa:', 'guarantee' => '100% Tyytyväinen tai Rahat Takaisin'],
        'hu' => ['order_now' => 'RENDELJEN MOST', 'name' => 'Teljes név', 'phone' => 'Telefon', 'city' => 'Város', 'address' => 'Cím', 'note' => 'Megjegyzés', 'send_order' => 'Rendelés küldése', 'cod' => 'UTÁNVÉT', 'limited_stock' => 'KORLÁTOZOTT KÉSZLET', 'only_today' => 'Az ajánlat csak ma érvényes!', 'customers' => 'Ügyfelek', 'rating' => 'Értékelés', 'reviews' => 'Vélemények', 'why_choose' => 'Miért válassza ezt a terméket?', 'how_to_order' => 'HOGYAN RENDELJEN', 'step1_t' => 'Töltse ki az űrlapot', 'step1_d' => 'Adatai 100% -ban biztonságban vannak', 'step2_t' => 'Felhívjuk Önt', 'step2_d' => 'Rendelésének megerősítéséhez', 'step3_t' => 'Házhozszállítás', 'step3_d' => 'Érkezéskor fizet', 'testimonials' => 'Mit mondanak az ügyfeleink', 'share' => 'Megosztás:', 'guarantee' => '100% Elégedett vagy Pénzvisszafizetés'],
        'ro' => ['order_now' => 'COMANDĂ ACUM', 'name' => 'Nume complet', 'phone' => 'Telefon', 'city' => 'Oraș', 'address' => 'Adresă', 'note' => 'Notă', 'send_order' => 'Trimite comanda', 'cod' => 'PLATĂ RAMBURS', 'limited_stock' => 'STOC LIMITAT', 'only_today' => 'Oferta valabilă doar astăzi!', 'customers' => 'Clienți', 'rating' => 'Evaluare', 'reviews' => 'Recenzii', 'why_choose' => 'De ce să alegi acest produs?', 'how_to_order' => 'CUM COMANZI', 'step1_t' => 'Completează formularul', 'step1_d' => 'Informațiile tale sunt 100% sigure', 'step2_t' => 'Te sunăm', 'step2_d' => 'Pentru a confirma comanda ta', 'step3_t' => 'Livrare la domiciliu', 'step3_d' => 'Plătește la primire', 'testimonials' => 'Ce spun clienții noștri', 'share' => 'Distribuie:', 'guarantee' => '100% Satisfăcut sau Bani înapoi'],
        'uk' => ['order_now' => 'ЗАМОВИТИ ЗАРАЗ', 'name' => 'Повне ім\'я', 'phone' => 'Телефон', 'city' => 'Місто', 'address' => 'Адреса', 'note' => 'Примітка', 'send_order' => 'Надіслати замовлення', 'cod' => 'ОПЛАТА ПРИ ДОСТАВЦІ', 'limited_stock' => 'ОБМЕЖЕНИЙ ЗАПАС', 'only_today' => 'Пропозиція діє тільки сьогодні!', 'customers' => 'Клієнти', 'rating' => 'Оцінка', 'reviews' => 'Відгуки', 'why_choose' => 'Чому обрати цей продукт?', 'how_to_order' => 'ЯК ЗАМОВИТИ', 'step1_t' => 'Заповніть форму', 'step1_d' => 'Ваша інформація 100% в безпеці', 'step2_t' => 'Ми вам зателефонуємо', 'step2_d' => 'Щоб підтвердити ваше замовлення', 'step3_t' => 'Доставка додому', 'step3_d' => 'Оплата при отриманні', 'testimonials' => 'Що кажуть наші клієнти', 'share' => 'Поділитися:', 'guarantee' => '100% Задоволений або Повернення Грошей'],
        'sw' => ['order_now' => 'AGIZA SASA', 'name' => 'Jina kamili', 'phone' => 'Simu', 'city' => 'Jiji', 'address' => 'Anwani', 'note' => 'Kumbuka', 'send_order' => 'Tuma agizo', 'cod' => 'LIPA WAKATI WA UTOAJI', 'limited_stock' => 'HIFADHI NDOGO', 'only_today' => 'Ofa inafaa leo tu!', 'customers' => 'Wateja', 'rating' => 'Ukadiriaji', 'reviews' => 'Ukaguzi', 'why_choose' => 'Kwa nini uchague bidhaa hii?', 'how_to_order' => 'JINSI YA KUAGIZA', 'step1_t' => 'Jaza fomu', 'step1_d' => 'Taarifa zako ni salama 100%', 'step2_t' => 'Tutakuita', 'step2_d' => 'Kuthibitisha agizo lako', 'step3_t' => 'Utoaji wa nyumbani', 'step3_d' => 'Lipa wakati wa kufika', 'testimonials' => 'Wateja wetu wanasema', 'share' => 'Shiriki:', 'guarantee' => '100% Kuridhika au Urejesho wa Fedha'],
        'bn' => ['order_now' => 'এখনই অর্ডার করুন', 'name' => 'পুরো নাম', 'phone' => 'ফোন', 'city' => 'শহর', 'address' => 'ঠিকানা', 'note' => 'নোট', 'send_order' => 'অর্ডার পাঠান', 'cod' => 'ডেলিভারি-তে নগদ অর্থ', 'limited_stock' => 'সীমিত স্টক', 'only_today' => 'অফার কেবল আজ বৈধ!', 'customers' => 'গ্রাহকরা', 'rating' => 'রেটিং', 'reviews' => 'পর্যালোচনা', 'why_choose' => 'কেন এই পণ্য নির্বাচন?', 'how_to_order' => 'কীভাবে অর্ডার করবেন', 'step1_t' => 'ফর্ম পূরণ করুন', 'step1_d' => 'আপনার তথ্য 100% নিরাপদ', 'step2_t' => 'আমরা আপনাকে কল করব', 'step2_d' => 'আপনার আদেশ নিশ্চিত করতে', 'step3_t' => 'হোম ডেলিভারি', 'step3_d' => 'আগমনের সময় পরিশোধ', 'testimonials' => 'আমাদের গ্রাহকরা কি বলেন', 'share' => 'শেয়ার করুন:', 'guarantee' => '100% সন্তুষ্ট বা ফেরত'],
        'fa' => ['order_now' => 'همین حالا سفارش دهید', 'name' => 'نام کامل', 'phone' => 'تلفن', 'city' => 'شهر', 'address' => 'آدرس', 'note' => 'یادداشت', 'send_order' => 'ارسال سفارش', 'cod' => 'پرداخت در محل', 'limited_stock' => 'موجودی محدود', 'only_today' => 'پیشنهاد فقط امروز معتبر است!', 'customers' => 'مشتریان', 'rating' => 'رتبه بندی', 'reviews' => 'نظرات', 'why_choose' => 'چرا این محصول را انتخاب کنید؟', 'how_to_order' => 'نحوه سفارش', 'step1_t' => 'فرم را پر کنید', 'step1_d' => 'اطلاعات شما 100% ایمن است', 'step2_t' => 'ما با شما تماس می‌گیریم', 'step2_d' => 'برای تأیید سفارش شما', 'step3_t' => 'تحویل درب منزل', 'step3_d' => 'هنگام تحویل پرداخت کنید', 'testimonials' => 'نظر مشتریان ما', 'share' => 'اشتراک‌گذاری:', 'guarantee' => '100% راضی یا بازپرداخت'],
        'ur' => ['order_now' => 'ابھی آرڈر کریں', 'name' => 'مکمل نام', 'phone' => 'فون', 'city' => 'شہر', 'address' => 'پتہ', 'note' => 'نوٹ', 'send_order' => 'آرڈر بھیجیں', 'cod' => 'ڈیلیوری پر کیش', 'limited_stock' => 'محدود اسٹاک', 'only_today' => 'پیشکش صرف آج کے لیے درست!', 'customers' => 'صارفین', 'rating' => 'درجہ بندی', 'reviews' => 'جائزے', 'why_choose' => 'اس پروڈکٹ کو کیوں منتخب کریں؟', 'how_to_order' => 'آرڈر کیسے کریں', 'step1_t' => 'فارم بھریں', 'step1_d' => 'آپ کی معلومات 100% محفوظ', 'step2_t' => 'ہم آپ کو کال کریں گے', 'step2_d' => 'آپ کے آرڈر کی تصدیق کے لیے', 'step3_t' => 'گھر ڈیلیوری', 'step3_d' => 'آمد پر ادائیگی', 'testimonials' => 'ہمارے گاہک کیا کہتے ہیں', 'share' => 'شیئر کریں:', 'guarantee' => '100% مطمئن یا رقم واپس'],
    ];
    
    // Fill any missing languages with English as fallback
    foreach (array_keys($i18n) as $lang) {
        foreach ($i18n['en'] as $key => $value) {
            if (!isset($i18n[$lang][$key])) {
                $i18n[$lang][$key] = $value;
            }
        }
    }

    $testimonials = [
        ['name' => 'Karim', 'city' => 'Casablanca', 'fr' => "Produit de qualité, livraison rapide. Je recommande vivement !", 'en' => 'Great product, fast delivery. Highly recommend!', 'ar' => 'منتج ممتاز والتوصيل سريع جدا. أنصح به بشدة !'],
        ['name' => 'Fatima', 'city' => 'Rabat', 'fr' => "Exactement comme décrit. Service client au top.", 'en' => 'Exactly as described. Great customer service.', 'ar' => 'تماما كما هو موضح. خدمة العملاء ممتازة.'],
        ['name' => 'Youssef', 'city' => 'Marrakech', 'fr' => "J'étais hésitant mais au final très satisfait. Merci !", 'en' => 'I was hesitant but in the end very satisfied. Thank you!', 'ar' => 'كنت مترددا لكن في النهاية راض جدا. شكرا !'],
    ];
@endphp
<body class="antialiased bg-[#f5f5f0]" :class="{'rtl': rtlLangs.includes(currentLang)}" x-init="i18n = @js($i18n); badgeLabels = @js($badgeLabels); testimonialsData = @js($testimonials); pageData = @js($pageData)">

    <!-- Top promo marquee -->
    @php
        $headerItems = $td['header_items'] ?? [];
        $hasCustomHeaderItems = !empty($headerItems) && is_array($headerItems);
    @endphp
    <div class="bg-black text-white text-xs font-bold py-2 overflow-hidden whitespace-nowrap relative">
        <div class="flex animate-marquee gap-8 w-max pl-8">
            @for($i = 0; $i < 2; $i++)
                @if($hasCustomHeaderItems)
                    @foreach($headerItems as $index => $item)
                        @if(!empty($item['text']))
                            <span>{{ $item['emoji'] ?? '🔥' }} {{ $item['text'] }}</span>
                            <span>•</span>
                        @endif
                    @endforeach
                @else
                    <span>🔥 {{ $promoBadge }}</span>
                    <span>•</span>
                    <span x-text="t('limited_stock')">{{ $i18n['fr']['limited_stock'] }}</span>
                    <span>•</span>
                    <span>🚚 {{ $badgeLabels['fr']['free_shipping'][1] }}</span>
                    <span>•</span>
                    <span>💵 {{ $badgeLabels['fr']['cod'][1] }}</span>
                    <span>•</span>
                @endif
            @endfor
        </div>
    </div>

    <!-- Language Switcher -->
    @php
        $enabledLanguages = $product->landing_page_languages ?? ['fr'];
        $currencySymbol = match($product->landing_page_currency ?? 'MAD') {
            'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'SAR' => 'ر.س', 'AED' => 'د.إ',
            'JPY' => '¥', 'CNY' => '¥', 'INR' => '₹', 'KRW' => '₩', 'RUB' => '₽',
            'TRY' => '₺', 'CAD' => 'C$', 'AUD' => 'A$', 'CHF' => 'CHF', 'BRL' => 'R$',
            'TZS' => 'TSh', 'KES' => 'KSh', 'EGP' => 'E£', 'NGN' => '₦', 'ZAR' => 'R',
            'THB' => '฿', 'IDR' => 'Rp', 'VND' => '₫', 'PHP' => '₱', 'MYR' => 'RM',
            'SGD' => 'S$', 'HKD' => 'HK$', 'TWD' => 'NT$', 'NZD' => 'NZ$',
            'DKK' => 'kr', 'NOK' => 'kr', 'SEK' => 'kr', 'PLN' => 'zł', 'CZK' => 'Kč',
            'HUF' => 'Ft', 'RON' => 'lei', 'UAH' => '₴', 'ILS' => '₪',
            default => 'د.م.'
        };
        $currencyCode = $product->landing_page_currency ?? 'MAD';
        
        $languageCodes = [
            'fr' => 'FR', 'en' => 'EN', 'ar' => 'AR', 'es' => 'ES', 'de' => 'DE',
            'it' => 'IT', 'pt' => 'PT', 'ru' => 'RU', 'zh' => 'ZH', 'ja' => 'JA',
            'ko' => 'KO', 'tr' => 'TR', 'nl' => 'NL', 'pl' => 'PL', 'hi' => 'HI',
            'th' => 'TH', 'vi' => 'VI', 'id' => 'ID', 'ms' => 'MS', 'he' => 'HE',
            'el' => 'EL', 'cs' => 'CS', 'sv' => 'SV', 'no' => 'NO', 'da' => 'DA',
            'fi' => 'FI', 'hu' => 'HU', 'ro' => 'RO', 'uk' => 'UK', 'sw' => 'SW',
            'bn' => 'BN', 'fa' => 'FA', 'ur' => 'UR',
        ];
    @endphp
    @if(count($enabledLanguages) > 1)
    <div class="fixed top-2 right-2 z-50 bg-white shadow-lg rounded-full p-0.5 flex gap-0.5 border border-gray-200 max-w-[calc(100vw-1rem)] overflow-x-auto flex-nowrap">
        @foreach($enabledLanguages as $lang)
            @if(isset($languageCodes[$lang]))
            <button @click="currentLang = '{{ $lang }}'" 
                    :class="currentLang === '{{ $lang }}' ? 'bg-gray-900 text-white' : 'text-gray-700'" 
                    class="px-2.5 py-1 rounded-full font-bold text-[10px] transition whitespace-nowrap flex-shrink-0">
                {{ $languageCodes[$lang] }}
            </button>
            @endif
        @endforeach
    </div>
    @endif

    <!-- HERO: Big bold product title + image + order form + price -->
    <section class="relative bg-gradient-to-br {{ $heroBg }} stripe-bg text-white overflow-hidden">
        <div class="container mx-auto px-4 py-6 md:py-12 relative z-10 max-w-6xl">
            <div class="grid lg:grid-cols-2 gap-6 items-start">
                <!-- Left: Title + image + price -->
                <div class="text-center lg:text-left space-y-4">
                    @if($shortDescription)
                    <div class="inline-block bg-yellow-300 text-gray-900 px-3 py-1 rounded-md text-xs font-extrabold uppercase tracking-wider shadow-md">
                        {{ $shortDescription }}
                    </div>
                    @endif

                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-black uppercase leading-tight drop-shadow-[0_4px_0_rgba(0,0,0,0.25)]" style="color: {{ $titleColor }}; font-family: {{ $titleFontFamily }}; letter-spacing: 0.02em;">
                        {{ $product->name }}
                    </h1>

                    @if(!empty($images))
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <div class="absolute -inset-4 bg-white/20 rounded-[40px] blur-xl"></div>
                        <div class="relative bg-white rounded-3xl p-3 shadow-2xl border-4 border-white">
                            <img src="{{ $images[0] }}" alt="{{ $product->name }}" class="w-full h-auto object-contain rounded-2xl" style="max-height: 420px;">
                        </div>
                    </div>
                    @endif

                    <!-- Big price display -->
                    <div class="flex items-center justify-center lg:justify-start gap-4 pt-2">
                        <div class="bg-white text-gray-900 px-6 py-3 rounded-2xl shadow-xl">
                            <div class="flex items-baseline gap-2">
                                <span class="font-display text-5xl md:text-6xl font-black">{{ number_format($product->price, 0) }}</span>
                                <span class="text-xl font-bold text-gray-600">{{ $currencyCode }}</span>
                            </div>
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                            <div class="text-sm line-through text-red-500 text-center font-semibold">{{ number_format($product->compare_at_price, 0) }} {{ $currencyCode }}</div>
                            @endif
                        </div>
                        @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <div class="bg-yellow-300 text-red-700 font-black text-2xl md:text-3xl px-4 py-2 rounded-xl rotate-[-8deg] shadow-lg animate-pulse-scale">
                            -{{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}%
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Order form card -->
                <div class="bg-white rounded-3xl shadow-2xl p-5 md:p-7 text-gray-900 border-4 border-yellow-300" id="order-form">
                    <div class="text-center mb-4">
                        <div class="inline-block bg-red-500 text-white px-4 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider mb-2">
                            <span x-text="t('cod')">{{ $i18n['fr']['cod'] }}</span>
                        </div>
                        <h2 class="font-display text-3xl md:text-4xl font-black uppercase text-gray-900"
                            x-text="t('order_now')">
                            {{ $i18n['fr']['order_now'] }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-1 font-semibold"
                            x-text="t('only_today')">
                            {{ $i18n['fr']['only_today'] }}
                        </p>
                    </div>

                    @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded-lg text-sm text-center">
                        {{ session('success') }}
                    </div>
                    @endif

                    <!-- Quantity-Based Promotions -->
                    @if($product->has_promotions && $product->activePromotions->isNotEmpty())
                    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl p-4 border-2 border-yellow-300 mb-4">
                        <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="text-xl">💰</span>
                            <span x-text="currentLang === 'ar' ? 'عروض الكمية' : (currentLang === 'sw' ? 'Ofa za Kiasi' : (currentLang === 'en' ? 'Quantity Deals' : 'Offres de Quantité'))">Offres de Quantité</span>
                        </h3>
                        <div class="space-y-2" id="promotionsContainerForm">
                            @foreach($product->activePromotions as $index => $promotion)
                            <label class="block p-3 bg-white rounded-xl border-2 cursor-pointer hover:border-yellow-400 transition promotion-option-form {{ $index === 0 ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-gray-200' }}"
                                   data-promotion-id="{{ $promotion->id }}"
                                   data-min-quantity="{{ $promotion->min_quantity }}"
                                   data-max-quantity="{{ $promotion->max_quantity ?? '' }}"
                                   data-price="{{ $promotion->price }}"
                                   data-discount="{{ $promotion->discount_percentage }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" 
                                           name="selected_promotion_form" 
                                           value="{{ $promotion->id }}" 
                                           class="w-5 h-5 text-yellow-500 focus:ring-yellow-400"
                                           {{ $index === 0 ? 'checked' : '' }}
                                           onchange="updatePromotionDisplayForm(this)">
                                    <div class="flex-1 flex items-center justify-between">
                                        <div class="font-semibold text-gray-700 text-sm">
                                            <span x-text="currentLang === 'ar' ? 'اشتري' : (currentLang === 'sw' ? 'Nunua' : (currentLang === 'en' ? 'Buy' : 'Achetez'))">Achetez</span>
                                            {{ $promotion->quantity_range }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-black text-gray-900">{{ number_format($promotion->price, 2) }} {{ $currencyCode }}</span>
                                            @if($promotion->discount_percentage > 0)
                                            <span class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full font-bold">
                                                -{{ $promotion->discount_percentage }}%
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Variations Selector -->
                    @if($product->has_variations && $product->activeVariations->isNotEmpty())
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-4 border-2 border-blue-200 mb-4">
                        <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="text-xl">🎨</span>
                            <span x-text="currentLang === 'ar' ? 'الخيارات المتاحة' : (currentLang === 'sw' ? 'Chaguo zinazopatikana' : (currentLang === 'en' ? 'Available Options' : 'Options disponibles'))">Options disponibles</span>
                        </h3>
                        <div class="space-y-2" id="variationsContainerForm">
                            @foreach($product->activeVariations as $index => $variation)
                            @php
                                $displayName = '';
                                if (!empty($variation->attributes) && is_array($variation->attributes)) {
                                    $attrParts = [];
                                    foreach ($variation->attributes as $key => $value) {
                                        $attrParts[] = ucfirst($key) . ': ' . $value;
                                    }
                                    $displayName = implode(' / ', $attrParts);
                                }
                                if (empty($displayName)) {
                                    $displayName = 'Option ' . ($index + 1);
                                }
                            @endphp
                            <label class="block p-3 bg-white rounded-xl border-2 cursor-pointer hover:border-blue-400 transition variation-option-form {{ $variation->is_default ? 'border-blue-400 ring-2 ring-blue-200' : 'border-gray-200' }}"
                                   data-variation-id="{{ $variation->id }}"
                                   data-price="{{ $variation->price }}"
                                   data-compare-price="{{ $variation->compare_at_price ?? 0 }}"
                                   data-discount="{{ $variation->discount_percentage }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" 
                                           name="selected_variation_form" 
                                           value="{{ $variation->id }}" 
                                           class="w-5 h-5 text-blue-500 focus:ring-blue-400"
                                           {{ $variation->is_default ? 'checked' : '' }}
                                           onchange="updateVariationDisplayForm(this)">
                                    <div class="flex-1 flex items-center justify-between">
                                        <div>
                                            <div class="font-semibold text-gray-900 text-sm">{{ $displayName }}</div>
                                            <div class="text-xs text-gray-500">
                                                <span x-text="(currentLang === 'ar' ? 'المخزون: ' : (currentLang === 'sw' ? 'Hifadhi: ' : (currentLang === 'en' ? 'Stock: ' : 'Stock: '))) + '{{ $variation->stock }}'">Stock: {{ $variation->stock }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-black text-gray-900">{{ number_format($variation->price, 2) }} {{ $currencyCode }}</div>
                                            @if($variation->compare_at_price && $variation->compare_at_price > $variation->price)
                                            <div class="text-xs line-through text-gray-400">{{ number_format($variation->compare_at_price, 2) }} {{ $currencyCode }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('store.product.submit-lead', [$store->subdomain, $product->slug]) }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="language" :value="currentLang">

                        <input type="text" name="name" required
                            :placeholder="t('name')"
                            class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 text-gray-900 font-semibold">

                        <input type="tel" name="phone" required
                            :placeholder="t('phone')"
                            class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 text-gray-900 font-semibold">

                        <textarea name="note" rows="2"
                            :placeholder="t('note')"
                            class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 text-gray-900 font-semibold"></textarea>

                        <button type="submit" class="w-full bg-gradient-to-r {{ $ctaBg }} text-white font-display text-2xl md:text-3xl uppercase py-4 rounded-xl shadow-xl hover:shadow-2xl transition-all hover:-translate-y-0.5 animate-pulse-scale">
                            ✓ <span x-text="t('send_order')">{{ $ctaText }}</span>
                        </button>
                    </form>

                    <!-- Trust badges row -->
                    <div class="flex flex-wrap justify-center gap-2 mt-4 pt-4 border-t border-gray-200">
                        @if(!empty($customTrustBadges))
                            {{-- Custom trust badges from theme_data --}}
                            @foreach(array_slice($customTrustBadges, 0, 4) as $customBadge)
                                @if(!empty($customBadge['text']))
                                <div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
                                    <span>{{ $customBadge['emoji'] ?? '✅' }}</span>
                                    <span>{{ $customBadge['text'] }}</span>
                                </div>
                                @endif
                            @endforeach
                        @else
                            {{-- Fallback to predefined badges --}}
                            @foreach(array_slice(empty($badges) ? ['free_shipping','money_back','secure_payment','warranty'] : $badges, 0, 4) as $badgeKey)
                                @php $b = $badgeLabels['fr'][$badgeKey] ?? null; @endphp
                                @if($b)
                                <div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
                                    <span>{{ $b[0] }}</span>
                                    <span x-text="badge('{{ $badgeKey }}')">{{ $b[1] }}</span>
                                </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Red stats bar with big numbers -->
    <section class="bg-red-600 text-white py-5 md:py-7 relative overflow-hidden stripe-bg">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="grid grid-cols-3 gap-3 md:gap-6 text-center">
                <div>
                    <div class="font-display text-3xl md:text-5xl font-black text-yellow-300">{{ $statsCustomers }}+</div>
                    <div class="text-xs md:text-sm font-bold uppercase tracking-wider mt-1"
                        x-text="t('customers')">{{ $i18n['fr']['customers'] }}</div>
                </div>
                <div class="border-x-2 border-red-500/70">
                    <div class="font-display text-3xl md:text-5xl font-black text-yellow-300">⭐ {{ $statsRating }}</div>
                    <div class="text-xs md:text-sm font-bold uppercase tracking-wider mt-1"
                        x-text="t('rating')">{{ $i18n['fr']['rating'] }}</div>
                </div>
                <div>
                    <div class="font-display text-3xl md:text-5xl font-black text-yellow-300">{{ $statsReviews }}</div>
                    <div class="text-xs md:text-sm font-bold uppercase tracking-wider mt-1"
                        x-text="t('reviews')">{{ $i18n['fr']['reviews'] }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content sections from user uploads (alternating colored banners) -->
    @if(!empty($product->landing_page_sections))
        @php $sectionColors = ['bg-gray-900 text-white', 'bg-red-500 text-white', 'bg-amber-400 text-gray-900', 'bg-emerald-600 text-white', 'bg-indigo-600 text-white']; @endphp
        @foreach($product->landing_page_sections as $i => $section)
            @php
                $sectionImg = null;
                if (!empty($section['image'])) {
                    $sectionImg = \App\Models\Product::resolvePublicImageUrl($section['image']);
                } elseif (isset($section['image_index']) && isset($images[$section['image_index']])) {
                    $sectionImg = $images[$section['image_index']];
                }
                
                $sectionTranslations = $section['translations'] ?? [];
                if (empty($sectionTranslations)) {
                    foreach (['fr', 'en', 'ar'] as $legacyLang) {
                        if (!empty($section["title_{$legacyLang}"])) {
                            $sectionTranslations[$legacyLang] = [
                                'title' => $section["title_{$legacyLang}"] ?? '',
                                'description' => $section["description_{$legacyLang}"] ?? '',
                            ];
                        }
                    }
                }
                
                $fallbackTitle = '';
                $fallbackDesc = '';
                foreach ($sectionTranslations as $data) {
                    if (!empty($data['title'])) {
                        $fallbackTitle = $data['title'];
                        $fallbackDesc = $data['description'] ?? '';
                        break;
                    }
                }
                
                $bandClass = $sectionColors[$i % count($sectionColors)];
            @endphp

            @if($fallbackTitle)
            <div class="{{ $bandClass }} py-4 md:py-6 relative stripe-bg"
                 x-data="{ sectionTranslations: @js($sectionTranslations), fallbackTitle: @js($fallbackTitle), fallbackDesc: @js($fallbackDesc) }">
                <div class="container mx-auto px-4 max-w-4xl text-center">
                    <h2 class="font-display text-3xl md:text-5xl font-black uppercase tracking-wide"
                        x-text="sectionTranslations[currentLang]?.title || fallbackTitle">{{ $fallbackTitle }}</h2>
                </div>
            </div>
            @endif

            @if($sectionImg || $fallbackDesc)
            <section class="py-6 md:py-10 bg-white"
                     x-data="{ sectionTranslations: @js($sectionTranslations), fallbackDesc: @js($fallbackDesc) }">
                <div class="container mx-auto px-4 max-w-4xl space-y-5">
                    @if($sectionImg)
                    <div class="rounded-2xl overflow-hidden shadow-xl border-4 border-white ring-1 ring-gray-200">
                        <img src="{{ $sectionImg }}" alt="{{ $fallbackTitle }}" class="w-full h-auto object-cover">
                    </div>
                    @endif
                    @if($fallbackDesc)
                    <p class="text-gray-700 text-base md:text-lg leading-relaxed text-center font-medium"
                       x-text="sectionTranslations[currentLang]?.description || fallbackDesc">{{ $fallbackDesc }}</p>
                    @endif
                </div>
            </section>
            @endif
        @endforeach
    @endif

    <!-- Product description -->
    @if($product->description)
    <section class="py-6 md:py-10 bg-[#f5f5f0]">
        <div class="container mx-auto px-4 max-w-3xl">
            <div class="prose prose-lg max-w-none text-gray-800 font-medium text-center">
                {!! $product->description !!}
            </div>
        </div>
    </section>
    @endif

    <!-- Why choose / Features (Dynamic from AI) -->
    <section class="py-10 md:py-14 bg-gray-900 text-white relative overflow-hidden" x-show="getFeatures().length > 0">
        <div class="absolute inset-0 stripe-bg opacity-60"></div>
        <div class="container mx-auto px-4 max-w-5xl relative z-10">
            <h2 class="font-display text-3xl md:text-5xl font-black text-center uppercase mb-8 text-yellow-300"
                x-text="t('why_choose')">
                {{ $i18n['fr']['why_choose'] ?? 'Why Choose This Product?' }}
            </h2>
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
                <template x-for="(feature, index) in getFeatures()" :key="index">
                    <div class="bg-white text-gray-900 rounded-2xl p-5 shadow-lg border-b-4 border-yellow-300 text-center hover:-translate-y-1 transition">
                        <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center text-3xl mb-3 mx-auto">
                            <span x-text="feature.icon || '✨'"></span>
                        </div>
                        <h3 class="font-bold text-base mb-1" x-text="feature.title"></h3>
                        <p class="text-sm text-gray-600 leading-snug" x-text="feature.description"></p>
                    </div>
                </template>
            </div>
        </div>
    </section>
    
    <!-- Fallback: Static Features from theme_data (if no AI features) -->
    @if(!empty(array_filter($staticFeatures, fn($f) => !empty($f['text']))))
    <section class="py-10 md:py-14 bg-gray-900 text-white relative overflow-hidden" x-show="getFeatures().length === 0">
        <div class="absolute inset-0 stripe-bg opacity-60"></div>
        <div class="container mx-auto px-4 max-w-5xl relative z-10">
            <h2 class="font-display text-3xl md:text-5xl font-black text-center uppercase mb-8 text-yellow-300"
                x-text="t('why_choose')">
                {{ $i18n['fr']['why_choose'] ?? 'Why Choose This Product?' }}
            </h2>
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($staticFeatures as $feature)
                    @if(!empty($feature['text']))
                    <div class="bg-white text-gray-900 rounded-2xl p-5 shadow-lg border-b-4 border-yellow-300 text-center hover:-translate-y-1 transition">
                        <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center text-3xl mb-3 mx-auto">
                            {{ $featureIconMap[$feature['icon'] ?? 'clean'] ?? '✨' }}
                        </div>
                        <p class="font-bold text-sm leading-snug">{{ $feature['text'] }}</p>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Big CTA banner (order again) -->
    <section class="bg-gradient-to-r {{ $ctaBg }} text-white py-10 md:py-14 relative overflow-hidden stripe-bg">
        <div class="container mx-auto px-4 text-center max-w-3xl relative z-10">
            <div class="inline-block bg-yellow-300 text-gray-900 px-4 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider mb-4">
                🔥 {{ $promoBadge }}
            </div>
            <h2 class="font-display text-4xl md:text-6xl font-black uppercase mb-3 drop-shadow-lg" style="color: {{ $titleColor }};">
                {{ $product->name }}
            </h2>
            <p class="text-lg md:text-xl font-bold mb-6 opacity-95"
                x-text="t('guarantee')">
                {{ $i18n['fr']['guarantee'] }}
            </p>
            <a href="#order-form" class="inline-block bg-white text-gray-900 font-display text-2xl md:text-3xl uppercase px-8 py-4 rounded-2xl shadow-2xl hover:scale-105 transition-transform animate-pulse-scale">
                ➤ <span x-text="t('order_now')">{{ $ctaText }}</span>
            </a>
        </div>
    </section>

    <!-- How to order: 3 steps (Dynamic from AI or fallback) -->
    <section class="py-10 md:py-14 bg-white">
        <div class="container mx-auto px-4 max-w-5xl">
            <h2 class="font-display text-3xl md:text-5xl font-black text-center uppercase mb-10 text-gray-900"
                x-text="t('how_to_order')">
                {{ $i18n['fr']['how_to_order'] ?? 'How To Order' }}
            </h2>
            
            <!-- Dynamic steps from AI -->
            <div class="grid md:grid-cols-3 gap-5" x-show="getSteps().length > 0">
                <template x-for="(step, index) in getSteps()" :key="index">
                    <div class="text-center">
                        <div class="relative inline-block mb-3">
                            <div class="w-20 h-20 rounded-full text-white flex items-center justify-center text-4xl shadow-xl"
                                :class="index === 0 ? 'bg-red-500' : (index === 1 ? 'bg-amber-500' : 'bg-emerald-600')">
                                <span x-text="index === 0 ? '📝' : (index === 1 ? '📞' : '🚚')"></span>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-gray-900 text-yellow-300 flex items-center justify-center font-black text-lg shadow-md"
                                x-text="step.number || (index + 1)"></div>
                        </div>
                        <h3 class="font-black text-lg text-gray-900 mb-1" x-text="step.title"></h3>
                        <p class="text-sm text-gray-600 font-medium" x-text="step.description"></p>
                    </div>
                </template>
            </div>
            
            <!-- Fallback static steps -->
            <div class="grid md:grid-cols-3 gap-5" x-show="getSteps().length === 0">
                @foreach([1,2,3] as $n)
                @php
                    $icons = [1 => '📝', 2 => '📞', 3 => '🚚'];
                    $colors = [1 => 'bg-red-500', 2 => 'bg-amber-500', 3 => 'bg-emerald-600'];
                @endphp
                <div class="text-center">
                    <div class="relative inline-block mb-3">
                        <div class="w-20 h-20 rounded-full {{ $colors[$n] }} text-white flex items-center justify-center text-4xl shadow-xl">
                            {{ $icons[$n] }}
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-gray-900 text-yellow-300 flex items-center justify-center font-black text-lg shadow-md">{{ $n }}</div>
                    </div>
                    <h3 class="font-black text-lg text-gray-900 mb-1"
                        x-text="t('step{{ $n }}_t')">{{ $i18n['fr']["step{$n}_t"] ?? 'Step '.$n }}</h3>
                    <p class="text-sm text-gray-600 font-medium"
                        x-text="t('step{{ $n }}_d')">{{ $i18n['fr']["step{$n}_d"] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials (Dynamic from AI) -->
    <section class="py-10 md:py-14 bg-amber-50">
        <div class="container mx-auto px-4 max-w-6xl">
            <h2 class="font-display text-3xl md:text-5xl font-black text-center uppercase mb-8 text-gray-900"
                x-text="t('testimonials')">
                {{ $i18n['fr']['testimonials'] ?? 'What Our Customers Say' }}
            </h2>
            <div class="grid md:grid-cols-3 gap-5">
                <template x-for="(tst, index) in getTestimonials()" :key="index">
                    <div class="bg-white rounded-2xl p-5 shadow-md border-b-4 border-amber-300">
                        <div class="flex gap-1 text-yellow-400 text-xl mb-2">
                            <template x-for="star in (tst.rating || 5)" :key="star">
                                <span>★</span>
                            </template>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed mb-4" x-text="tst.text || tst.review || ''"></p>
                        <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-red-500 text-white font-black flex items-center justify-center"
                                x-text="(tst.name || 'A').charAt(0).toUpperCase()">
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 text-sm" x-text="tst.name || 'Customer'"></div>
                                <div class="text-xs text-gray-500" x-text="tst.city || ''"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- Trust badges strip -->
    @if(!empty($customTrustBadges) || !empty($badges))
    <section class="bg-white py-6 border-y-2 border-gray-100">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @if(!empty($customTrustBadges))
                    {{-- Custom trust badges from theme_data --}}
                    @foreach($customTrustBadges as $customBadge)
                        @if(!empty($customBadge['text']))
                        <div class="flex items-center gap-2 bg-gray-50 border-l-4 border-emerald-500 px-3 py-2.5 rounded-lg">
                            <span class="text-2xl">{{ $customBadge['emoji'] ?? '✅' }}</span>
                            <span class="font-bold text-gray-800 text-xs md:text-sm">
                                {{ $customBadge['text'] }}
                            </span>
                        </div>
                        @endif
                    @endforeach
                @else
                    {{-- Fallback to predefined badges --}}
                    @foreach($badges as $badgeKey)
                        @php $b = $badgeLabels['fr'][$badgeKey] ?? null; @endphp
                        @if($b)
                        <div class="flex items-center gap-2 bg-gray-50 border-l-4 border-emerald-500 px-3 py-2.5 rounded-lg">
                            <span class="text-2xl">{{ $b[0] }}</span>
                            <span class="font-bold text-gray-800 text-xs md:text-sm"
                                x-text="badge('{{ $badgeKey }}')">
                                {{ $b[1] }}
                            </span>
                        </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </section>
    @endif

    <!-- Sticky mobile order bar -->
    <a href="#order-form" class="fixed bottom-0 left-0 right-0 z-40 md:hidden bg-gradient-to-r {{ $ctaBg }} text-white font-display text-xl uppercase py-4 text-center shadow-2xl animate-pulse-scale">
        ➤ <span x-text="t('order_now')">{{ $ctaText }}</span>
        - {{ number_format($product->price, 0) }} {{ $currencyCode }}
    </a>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-6 pb-20 md:pb-6 text-center text-xs">
        <div class="container mx-auto px-4">
            <div class="font-bold text-white mb-1">{{ $store->name ?? 'Store' }}</div>
            © {{ date('Y') }} — All rights reserved.
        </div>
    </footer>

    @if($product->has_promotions && $product->activePromotions->isNotEmpty())
    <script>
        // Promotion selection handler for form
        function updatePromotionDisplayForm(radio) {
            const option = radio.closest('.promotion-option-form');
            const minQuantity = parseInt(option.dataset.minQuantity);
            const maxQuantity = option.dataset.maxQuantity ? parseInt(option.dataset.maxQuantity) : null;
            const price = parseFloat(option.dataset.price);
            const discount = option.dataset.discount;
            
            // Remove active state from all options
            document.querySelectorAll('.promotion-option-form').forEach(opt => {
                opt.classList.remove('border-yellow-400', 'ring-2', 'ring-yellow-200');
                opt.classList.add('border-gray-200');
            });
            
            // Add active state to selected option
            option.classList.remove('border-gray-200');
            option.classList.add('border-yellow-400', 'ring-2', 'ring-yellow-200');
        }
        
        // Set default promotion on page load
        document.addEventListener('DOMContentLoaded', function() {
            const defaultRadio = document.querySelector('input[name="selected_promotion_form"]:checked');
            if (defaultRadio) {
                updatePromotionDisplayForm(defaultRadio);
            }
        });
    </script>
    @endif

    @if($product->has_variations && $product->activeVariations->isNotEmpty())
    <script>
        // Variation selection handler for form
        function updateVariationDisplayForm(radio) {
            const option = radio.closest('.variation-option-form');
            const price = parseFloat(option.dataset.price);
            const comparePrice = parseFloat(option.dataset.comparePrice) || 0;
            const discount = option.dataset.discount;
            
            // Remove active state from all options
            document.querySelectorAll('.variation-option-form').forEach(opt => {
                opt.classList.remove('border-blue-400', 'ring-2', 'ring-blue-200');
                opt.classList.add('border-gray-200');
            });
            
            // Add active state to selected option
            option.classList.remove('border-gray-200');
            option.classList.add('border-blue-400', 'ring-2', 'ring-blue-200');
        }
        
        // Set default variation on page load
        document.addEventListener('DOMContentLoaded', function() {
            const defaultRadio = document.querySelector('input[name="selected_variation_form"]:checked');
            if (defaultRadio) {
                updateVariationDisplayForm(defaultRadio);
            }
        });
    </script>
    @endif

</body>
</html>
