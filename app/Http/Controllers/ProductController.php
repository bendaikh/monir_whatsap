<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index($subdomain, Request $request)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();
        
        $settings = \App\Models\WebsiteSettings::getSettings($store->user_id, $store->id);
        
        if (!$settings) {
            $settings = \App\Models\WebsiteSettings::getSettings($store->user_id, $store->id);
        }
        
        $query = Product::with('category')
            ->where('is_active', true)
            ->where('store_id', $store->id);

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('order')->orderBy('created_at', 'desc')->paginate(12);
        $categories = Category::where('is_active', true)
            ->where('store_id', $store->id)
            ->orderBy('order')
            ->get();
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->where('store_id', $store->id)
            ->limit(8)
            ->get();

        return view('welcome', compact('products', 'categories', 'featuredProducts', 'settings', 'store'));
    }

    public function show($subdomain, $slug)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();

        $product = Product::with(['activeVariations', 'activePromotions'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();
            
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->limit(4)
            ->get();

        if ($product->theme === 'theme2') {
            return view('product-landing-theme2', compact('product', 'relatedProducts', 'store'));
        }

        $hasLandingPageContent = !empty($product->landing_page_translations) 
            || $product->landing_page_fr 
            || $product->landing_page_en 
            || $product->landing_page_ar;
        
        if ($hasLandingPageContent) {
            return view('product-landing', compact('product', 'relatedProducts', 'store'));
        }

        return view('product-detail', compact('product', 'relatedProducts', 'store'));
    }

    public function submitLead(Request $request, $subdomain, $slug)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();
        
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string|max:1000',
            'language' => 'required|string|max:10',
        ]);

        $lead = \App\Models\ProductLead::create([
            'product_id' => $product->id,
            'user_id' => $product->user_id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'note' => $validated['note'],
            'language' => $validated['language'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        \App\Jobs\PushOrderToExternalApi::dispatch($lead);

        $successMessages = [
            'fr' => 'Merci ! Nous vous contactons bientôt.',
            'en' => 'Thank you! We will contact you soon.',
            'ar' => 'شكرا لك! سنتصل بك قريبا.',
            'es' => '¡Gracias! Nos pondremos en contacto pronto.',
            'de' => 'Danke! Wir werden Sie bald kontaktieren.',
            'it' => 'Grazie! Ti contatteremo presto.',
            'pt' => 'Obrigado! Entraremos em contato em breve.',
            'ru' => 'Спасибо! Мы скоро свяжемся с вами.',
            'zh' => '谢谢!我们很快会联系您。',
            'ja' => 'ありがとうございます!すぐにご連絡いたします。',
            'ko' => '감사합니다! 곧 연락드리겠습니다.',
            'nl' => 'Bedankt! We nemen binnenkort contact op.',
            'pl' => 'Dziękujemy! Wkrótce się z Tobą skontaktujemy.',
            'tr' => 'Teşekkürler! Kısa süre içinde sizinle iletişime geçeceğiz.',
            'hi' => 'धन्यवाद! हम जल्द ही आपसे संपर्क करेंगे।',
            'th' => 'ขอบคุณ! เราจะติดต่อคุณเร็วๆ นี้',
            'vi' => 'Cảm ơn bạn! Chúng tôi sẽ liên lạc sớm.',
            'id' => 'Terima kasih! Kami akan menghubungi Anda segera.',
            'ms' => 'Terima kasih! Kami akan menghubungi anda tidak lama lagi.',
            'he' => 'תודה! ניצור איתך קשר בקרוב.',
            'el' => 'Σας ευχαριστούμε! Θα επικοινωνήσουμε σύντομα.',
            'cs' => 'Děkujeme! Brzy se ozveme.',
            'sv' => 'Tack! Vi kontaktar dig snart.',
            'no' => 'Takk! Vi kontakter deg snart.',
            'da' => 'Tak! Vi kontakter dig snart.',
            'fi' => 'Kiitos! Otamme sinuun yhteyttä pian.',
            'hu' => 'Köszönjük! Hamarosan felvesszük Önnel a kapcsolatot.',
            'ro' => 'Multumim! Va vom contacta in curand.',
            'uk' => 'Дякуємо! Ми скоро зв\'яжемося з вами.',
            'sw' => 'Asante! Tutakuwasiliana hivi karibuni.',
            'bn' => 'ধন্যবাদ! আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।',
            'fa' => 'با تشکر! به زودی با شما تماس خواهیم گرفت.',
            'ur' => 'شکریہ! ہم جلد ہی آپ سے رابطہ کریں گے۔',
        ];

        return back()->with('success', $successMessages[$validated['language']] ?? $successMessages['fr']);
    }
}
