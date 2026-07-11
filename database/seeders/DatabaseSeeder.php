<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Attraction;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Event;
use App\Models\News;
use App\Models\CmsSetting;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\QrCode;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── CMS Settings ──────────────────────────────────────────────────────
        $cmsData = [
            ['key' => 'site_name',       'value' => 'Edo Odyssey',           'label' => 'Site Name',       'type' => 'text'],
            ['key' => 'contact_phone',   'value' => '+234 (0) 52 255 000',   'label' => 'Contact Phone',   'type' => 'text'],
            ['key' => 'contact_email',   'value' => 'info@edsta.edo.gov.ng', 'label' => 'Contact Email',   'type' => 'email'],
            ['key' => 'contact_address', 'value' => 'EDSTA HQ, Government House Road, GRA, Benin City, Edo State, Nigeria', 'label' => 'Address', 'type' => 'text'],
            ['key' => 'about_agency',    'value' => 'The Edo State Tourism Agency (EDSTA) is a statutory public corporation established to guide, preserve, license, and promote the natural landmarks, cultural festivals, and historical heritage monuments of Edo State — home to the ancient Benin Kingdom.', 'label' => 'About EDSTA', 'type' => 'textarea'],
            ['key' => 'footer_text',     'value' => '© 2026 Edo State Tourism Agency (EDSTA). All rights reserved.', 'label' => 'Footer Text', 'type' => 'text'],
            ['key' => 'meta_description','value' => 'Explore Edo State\'s world-class cultural heritage, wildlife, and tourism experiences through the official Edo Odyssey platform.', 'label' => 'Meta Description', 'type' => 'textarea'],
        ];
        foreach ($cmsData as $d) CmsSetting::firstOrCreate(['key' => $d['key']], $d);

        // ── Categories ────────────────────────────────────────────────────────
        $categories = [
            ['name' => 'Cultural Heritage', 'slug' => 'cultural-heritage', 'icon' => 'bi-building-fill',   'color' => '#1a3a6b'],
            ['name' => 'Nature & Wildlife', 'slug' => 'nature-wildlife',   'icon' => 'bi-tree-fill',        'color' => '#2d8a4e'],
            ['name' => 'Historical Sites',  'slug' => 'historical-sites',  'icon' => 'bi-bank2',            'color' => '#8b4513'],
            ['name' => 'Arts & Crafts',     'slug' => 'arts-crafts',       'icon' => 'bi-palette-fill',     'color' => '#c9a227'],
            ['name' => 'Religious Sites',   'slug' => 'religious-sites',   'icon' => 'bi-stars',            'color' => '#6b1a3a'],
            ['name' => 'Festivals',         'slug' => 'festivals',         'icon' => 'bi-balloon-heart-fill','color' => '#e05c27'],
        ];
        $catModels = [];
        foreach ($categories as $i => $c) {
            $catModels[] = Category::firstOrCreate(['slug' => $c['slug']], $c + ['sort_order' => $i]);
        }

        // ── Users (demo accounts) ─────────────────────────────────────────────
        $users = [
            ['email' => 'superadmin@edoodyssey.ng', 'first_name' => 'Frank',   'last_name' => 'Egbeobawaye', 'role' => 'Super Admin',  'heritage_points' => 750],
            ['email' => 'admin@edoodyssey.ng',      'first_name' => 'Comfort', 'last_name' => 'Obi',         'role' => 'Agency Admin', 'heritage_points' => 300],
            ['email' => 'tourist@edoodyssey.ng',    'first_name' => 'Akenzua', 'last_name' => 'Musa',        'role' => 'Tourist',      'heritage_points' => 120],
            ['email' => 'guide@edoodyssey.ng',      'first_name' => 'Osaro',   'last_name' => 'Edokpayi',    'role' => 'Guide',        'heritage_points' => 80],
            ['email' => 'hotel@edoodyssey.ng',      'first_name' => 'Patience','last_name' => 'Osagie',      'role' => 'Hotel',        'heritage_points' => 50],
            ['email' => 'restaurant@edoodyssey.ng', 'first_name' => 'Blessing','last_name' => 'Uwagboe',     'role' => 'Restaurant',   'heritage_points' => 40],
        ];
        $userModels = [];
        foreach ($users as $u) {
            $userModels[$u['email']] = User::firstOrCreate(
                ['email' => $u['email']],
                $u + ['password' => Hash::make('demo1234'), 'email_verified' => true]
            );
        }

        // Guide profile for Osaro
        $guideUser = $userModels['guide@edoodyssey.ng'];
        $guideModel = Guide::firstOrCreate(
            ['user_id' => $guideUser->id],
            [
                'bio'                 => 'Expert heritage guide specializing in Benin Kingdom history, bronze casting, and royal palace tours. Born and raised in Benin City with 10+ years of experience.',
                'languages'           => 'English, Yoruba, Bini, French',
                'specializations'     => 'Benin Kingdom History, Bronze Art, Royal Palaces, Forest Reserves',
                'experience'          => 10,
                'hourly_rate'         => 7500,
                'daily_rate'          => 40000,
                'certification'       => 'EDSTA Certified Guide (Level 3)',
                'verification_status' => 'Approved',
                'is_featured'         => true,
                'is_available'        => true,
            ]
        );

        // ── Attractions ───────────────────────────────────────────────────────
        $attractionData = [
            [
                'name'         => "Oba's Palace Complex",
                'description'  => "The magnificent royal palace of the Oba of Benin — one of the oldest royal compounds in the world. Home to centuries of Benin Kingdom history, bronze art, and living traditions.",
                'address'      => 'Oba Avenue, Benin City',
                'city'         => 'Benin City',
                'ticket_price' => 2000,
                'opening_hours'=> '9:00am – 5:00pm (Mon–Sat)',
                'latitude'     => 6.3374,
                'longitude'    => 5.6252,
                'image_url'    => 'https://images.unsplash.com/photo-1580654712603-eb43273aff33?auto=format&fit=crop&q=80&w=800',
                'is_featured'  => true,
                'category_index'=> 0,
            ],
            [
                'name'         => 'Benin National Museum',
                'description'  => "Houses one of Africa's most significant collections of Benin bronzes, ivory carvings, and royal regalia. A window into the sophisticated artistry of the ancient Benin Kingdom.",
                'address'      => 'King Square, Benin City',
                'city'         => 'Benin City',
                'ticket_price' => 1500,
                'opening_hours'=> '9:00am – 4:00pm (Tue–Sun)',
                'image_url'    => 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?auto=format&fit=crop&q=80&w=800',
                'is_featured'  => true,
                'category_index'=> 2,
            ],
            [
                'name'         => 'Okomu National Park',
                'description'  => "Nigeria's most accessible rainforest reserve — home to endangered forest elephants, chimpanzees, white-throated monkeys, and hundreds of rare bird species.",
                'address'      => 'Udo, Ovia South-West LGA',
                'city'         => 'Udo',
                'ticket_price' => 2500,
                'opening_hours'=> '7:00am – 6:00pm daily',
                'image_url'    => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&q=80&w=800',
                'is_featured'  => true,
                'category_index'=> 1,
            ],
            [
                'name'         => 'Igun Street Bronze Casters Guild',
                'description'  => "Watch master craftsmen practice the ancient lost-wax technique (cire perdue) that has been passed down through 600 years of unbroken tradition. The Guild was awarded UNESCO Intangible Heritage status.",
                'address'      => 'Igun Street, Benin City',
                'city'         => 'Benin City',
                'ticket_price' => 0,
                'opening_hours'=> '8:00am – 6:00pm (Mon–Sat)',
                'image_url'    => 'https://images.unsplash.com/photo-1547826039-bfc35e0f1ea8?auto=format&fit=crop&q=80&w=800',
                'is_featured'  => false,
                'category_index'=> 3,
            ],
        ];

        foreach ($attractionData as $a) {
            $catIndex = $a['category_index'];
            unset($a['category_index']);
            $attr = Attraction::firstOrCreate(
                ['name' => $a['name']],
                $a + ['category_id' => $catModels[$catIndex]->id ?? null, 'is_active' => true, 'views' => rand(50, 500)]
            );
            QrCode::generateForEntity('Attraction', $attr->id);
        }

        // ── Hotels ────────────────────────────────────────────────────────────
        $hotelData = [
            ['name' => 'Protea Hotel Benin City',   'stars' => 4, 'price_per_night' => 45000, 'description' => 'International-standard 4-star hotel in the heart of Benin City, offering modern amenities and warm Nigerian hospitality.', 'address' => 'Airport Road, Benin City', 'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80&w=800', 'is_featured' => true, 'amenities' => 'Pool, Gym, Restaurant, Bar, WiFi, Conference Rooms'],
            ['name' => 'Okada Hotels & Suites',     'stars' => 5, 'price_per_night' => 80000, 'description' => 'Luxury 5-star experience in Benin City with world-class dining, spa, and event facilities.', 'address' => 'Adesuwa Road, GRA, Benin City', 'image_url' => 'https://images.unsplash.com/photo-1529290130-4ca3753253ae?auto=format&fit=crop&q=80&w=800', 'is_featured' => true, 'amenities' => 'Pool, Spa, Restaurant, Bar, WiFi, Business Centre, Gym'],
            ['name' => 'Heritage Inn Benin',        'stars' => 3, 'price_per_night' => 18000, 'description' => 'Comfortable budget-friendly accommodation near the city centre, ideal for business and leisure travellers.', 'address' => 'Akpakpava Road, Benin City', 'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&q=80&w=800', 'is_featured' => false, 'amenities' => 'WiFi, Restaurant, Parking'],
        ];
        foreach ($hotelData as $h) {
            Hotel::firstOrCreate(['name' => $h['name']], $h + ['is_active' => true, 'city' => 'Benin City', 'user_id' => $userModels['hotel@edoodyssey.ng']->id]);
        }

        // ── Restaurants ───────────────────────────────────────────────────────
        $restaurantData = [
            ['name' => "Mama Afua's Kitchen",    'cuisine_type' => 'Nigerian Traditional', 'avg_price' => 3500, 'description' => 'Authentic Edo State cuisine in a warm, family atmosphere. Famous for groundnut soup, starch, and banga soup.', 'address' => 'Ring Road, Benin City', 'opening_hours' => '11am – 10pm', 'image_url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=800', 'is_featured' => true],
            ['name' => 'The Bronze Grill',       'cuisine_type' => 'Continental & Nigerian', 'avg_price' => 6000, 'description' => 'Upscale dining experience serving both Continental and Nigerian cuisine with a sophisticated atmosphere.', 'address' => 'GRA, Benin City', 'opening_hours' => '12pm – 11pm', 'image_url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&q=80&w=800', 'is_featured' => true],
        ];
        foreach ($restaurantData as $r) {
            Restaurant::firstOrCreate(['name' => $r['name']], $r + ['is_active' => true, 'city' => 'Benin City', 'user_id' => $userModels['restaurant@edoodyssey.ng']->id]);
        }

        // ── Events ────────────────────────────────────────────────────────────
        $eventsData = [
            ['name' => 'Igue Festival 2026', 'category' => 'Cultural Festival', 'ticket_price' => 0, 'start_date' => now()->addDays(45), 'end_date' => now()->addDays(47), 'location' => "Oba's Palace, Benin City", 'organizer' => "Office of the Oba of Benin", 'description' => "The Igue Festival is an ancient Benin Kingdom festival celebrating the spiritual powers of the Oba. A magnificent display of masquerades, dances, and royal ceremonies.", 'image_url' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&q=80&w=800', 'is_featured' => true, 'capacity' => 10000],
            ['name' => 'Edo Cultural and Creative Arts Festival', 'category' => 'Arts & Culture', 'ticket_price' => 2000, 'start_date' => now()->addDays(90), 'end_date' => now()->addDays(93), 'location' => 'Benin City Recreation Ground', 'organizer' => 'Edo State Government', 'description' => "A celebration of Edo art, music, fashion, and cuisine. Features live performances, exhibitions of Benin bronzes, and workshops by master craftsmen.", 'image_url' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80&w=800', 'is_featured' => true, 'capacity' => 5000],
        ];
        foreach ($eventsData as $e) {
            Event::firstOrCreate(['name' => $e['name']], $e + ['is_active' => true]);
        }

        // ── News ──────────────────────────────────────────────────────────────
        $newsData = [
            ['title' => "Edo State Tourism Revenue Hits ₦8.2 Billion in 2025", 'category' => 'Industry News', 'author' => 'EDSTA Communications', 'content' => "The Edo State Tourism Agency (EDSTA) has announced that tourism revenue for 2025 reached a record ₦8.2 billion, representing a 34% increase over 2024. The growth is attributed to the successful hosting of the Igue Festival, increased international visitor arrivals, and the launch of the Edo Odyssey digital platform.\n\nGovernor Godwin Obaseki credited the growth to sustained investment in heritage preservation and digital tourism infrastructure. 'Edo State is open for business, and our ancient culture is our greatest asset,' he stated.\n\nThe agency projects further growth in 2026 with the expansion of certified tour guide programs and new hotel developments in the GRA district.", 'is_featured' => true, 'image_url' => 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&q=80&w=800'],
            ['title' => 'UNESCO Considers Benin Bronzes for Enhanced Heritage Status', 'category' => 'Heritage', 'author' => 'Editorial Team', 'content' => "Delegations from UNESCO and the International Council of Museums (ICOM) met with EDSTA officials last week to discuss expanding the heritage designation of the Benin Bronze casting tradition. The ancient lost-wax casting technique, practiced uninterrupted for over 600 years along Igun Street, is already recognized as UNESCO Intangible Cultural Heritage.\n\nThe new proposal would create a cultural corridor linking the Igun Street Guild, the Benin National Museum, and the Oba's Palace Complex as a unified World Heritage Site.", 'is_featured' => false, 'image_url' => 'https://images.unsplash.com/photo-1547826039-bfc35e0f1ea8?auto=format&fit=crop&q=80&w=800'],
            ['title' => 'New Eco-Tourism Trail Opens at Okomu National Park', 'category' => 'Nature & Conservation', 'author' => 'Conservation Desk', 'content' => "A new 12-kilometre eco-tourism trail has opened at the Okomu National Park, offering visitors a guided immersive experience through one of Nigeria's last intact lowland rainforest ecosystems.\n\nThe trail features observation platforms for spotting the park's famous forest elephants, white-throated guenon monkeys, and over 150 recorded bird species. All guides are EDSTA-certified wildlife specialists with in-depth knowledge of the forest ecosystem.", 'is_featured' => true, 'image_url' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&q=80&w=800'],
        ];
        foreach ($newsData as $n) {
            News::firstOrCreate(['title' => $n['title']], $n + ['is_published' => true, 'author_user_id' => $userModels['admin@edoodyssey.ng']->id]);
        }

        // ── FAQs ──────────────────────────────────────────────────────────────
        $faqs = [
            ['q' => 'What is Edo Odyssey?', 'a' => 'Edo Odyssey is the official digital tourism platform of the Edo State Tourism Agency (EDSTA). It connects visitors with attractions, certified guides, hotels, restaurants, and events across Edo State.', 'cat' => 'General'],
            ['q' => 'How do I book a tour guide?', 'a' => 'Browse our list of certified guides, select one that meets your needs, and click "Book". You\'ll need a registered account. Your booking will be confirmed by admin within 24 hours.', 'cat' => 'Bookings'],
            ['q' => 'What are Heritage Points?', 'a' => 'Heritage Points are our loyalty program. Earn points by making bookings (20 pts), writing reviews (10 pts), adding favourites (5 pts), and completing tours (30 pts). Points unlock Explorer status levels.', 'cat' => 'Heritage Points'],
            ['q' => 'How do I become a certified tour guide?', 'a' => 'Register an account as a "Guide", complete your profile, and submit your credentials. EDSTA staff will review and verify your application typically within 3-5 business days.', 'cat' => 'For Guides'],
            ['q' => 'What is the best time to visit Edo State?', 'a' => 'Edo State can be visited year-round. The dry season (November–March) is ideal for sightseeing. The Igue Festival (December) and other cultural events make those periods especially vibrant.', 'cat' => 'Travel Tips'],
        ];
        foreach ($faqs as $i => $f) {
            Faq::firstOrCreate(['question' => $f['q']], ['answer' => $f['a'], 'category' => $f['cat'], 'sort_order' => $i, 'is_active' => true]);
        }

        // ── Partners ──────────────────────────────────────────────────────────
        $partners = [
            ['name' => 'Edo State Government',    'website' => 'https://edostate.gov.ng',    'sort_order' => 1],
            ['name' => 'Nigeria Tourism Development Corporation', 'website' => 'https://ntdc.gov.ng', 'sort_order' => 2],
            ['name' => 'UNESCO',                  'website' => 'https://unesco.org',          'sort_order' => 3],
            ['name' => 'Air Peace Airlines',      'website' => 'https://flyairpeace.com',     'sort_order' => 4],
        ];
        foreach ($partners as $p) {
            Partner::firstOrCreate(['name' => $p['name']], $p + ['is_active' => true]);
        }

        $this->command->info('✅ Edo Odyssey database seeded successfully!');
        $this->command->info('   Demo accounts: superadmin@edoodyssey.ng, tourist@edoodyssey.ng (password: demo1234)');
    }
}
