<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing services to avoid duplicates
        Service::truncate();

        $services = [
            // KNOTLESS BRAIDS
            [
                'name' => 'Knotless Braids - Small (Mid Back)',
                'description' => 'Elegant small knotless braids reaching mid-back length. Perfect for a sleek, natural look with minimal tension on your scalp.',
                'price' => 140.00,
                'duration' => 300,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Knotless Braids - Small (Waist Length)',
                'description' => 'Beautiful small knotless braids extending to waist length. Creates a stunning, flowing appearance with maximum versatility.',
                'price' => 180.00,
                'duration' => 360,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Knotless Braids - Medium (Mid Back)',
                'description' => 'Classic medium knotless braids reaching mid-back. Ideal balance of style and manageability for everyday wear.',
                'price' => 120.00,
                'duration' => 270,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Knotless Braids - Medium (Waist Length)',
                'description' => 'Stunning medium knotless braids extending to waist length. Perfect for those seeking length and volume with natural movement.',
                'price' => 160.00,
                'duration' => 330,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Knotless Braids - Large (Mid Back)',
                'description' => 'Bold large knotless braids reaching mid-back. Quick installation with a dramatic, statement-making appearance.',
                'price' => 100.00,
                'duration' => 240,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Knotless Braids - Large (Waist Length)',
                'description' => 'Striking large knotless braids extending to waist length. Creates a powerful, eye-catching look with easy maintenance.',
                'price' => 140.00,
                'duration' => 300,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Bohemian Knotless Braids (Mid Back)',
                'description' => 'Artistic bohemian knotless braids with textured ends and natural flow. Perfect for a carefree, bohemian aesthetic.',
                'price' => 160.00,
                'duration' => 330,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Bohemian Knotless Braids (Waist Length)',
                'description' => 'Gorgeous bohemian knotless braids with flowing waist-length extensions. Creates a romantic, free-spirited look.',
                'price' => 200.00,
                'duration' => 390,
                'category' => 'Hair Braiding',
                'active' => true
            ],

            // TWISTS
            [
                'name' => 'Medium Twists (Mid Back)',
                'description' => 'Classic medium twists reaching mid-back length. Versatile style that works for both casual and formal occasions.',
                'price' => 120.00,
                'duration' => 270,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Medium Twists (Waist Length)',
                'description' => 'Elegant medium twists extending to waist length. Creates a sophisticated, flowing appearance with natural movement.',
                'price' => 150.00,
                'duration' => 330,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Passion Twists (Mid Back)',
                'description' => 'Trendy passion twists with a natural, textured appearance. Perfect for those seeking a modern, effortless look.',
                'price' => 130.00,
                'duration' => 300,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Passion Twists (Waist Length)',
                'description' => 'Stunning passion twists extending to waist length. Creates a bold, fashion-forward statement with maximum impact.',
                'price' => 160.00,
                'duration' => 360,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Boho Twist (Mid Back)',
                'description' => 'Bohemian-inspired twists with natural texture and flow. Perfect for a relaxed, artistic aesthetic.',
                'price' => 150.00,
                'duration' => 300,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Boho Twist (Waist Length)',
                'description' => 'Gorgeous bohemian twists extending to waist length. Creates a romantic, free-spirited look with natural movement.',
                'price' => 180.00,
                'duration' => 360,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Spring Twists (Short)',
                'description' => 'Playful spring twists for shorter hair. Quick installation with a fun, bouncy appearance perfect for active lifestyles.',
                'price' => 100.00,
                'duration' => 180,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Spring Twists (Long)',
                'description' => 'Dynamic spring twists for longer hair. Creates a lively, energetic look with natural bounce and movement.',
                'price' => 130.00,
                'duration' => 240,
                'category' => 'Hair Braiding',
                'active' => true
            ],

            // SPECIALTY BRAIDS
            [
                'name' => 'Jungle Braids (Mid Back)',
                'description' => 'Wild and natural jungle braids reaching mid-back. Perfect for those seeking a bold, untamed aesthetic.',
                'price' => 130.00,
                'duration' => 300,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Jungle Braids (Waist Length)',
                'description' => 'Dramatic jungle braids extending to waist length. Creates a fierce, powerful look with maximum visual impact.',
                'price' => 160.00,
                'duration' => 360,
                'category' => 'Hair Braiding',
                'active' => true
            ],

            // LOCS
            [
                'name' => 'Soft Locs (Mid Back)',
                'description' => 'Gentle soft locs reaching mid-back length. Provides the loc aesthetic with a softer, more flexible texture.',
                'price' => 150.00,
                'duration' => 330,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Soft Locs (Waist Length)',
                'description' => 'Beautiful soft locs extending to waist length. Creates a stunning, flowing loc appearance with natural movement.',
                'price' => 180.00,
                'duration' => 390,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Butterfly Locs (Mid Back)',
                'description' => 'Elegant butterfly locs reaching mid-back length. Features a unique, textured appearance with natural flow.',
                'price' => 140.00,
                'duration' => 300,
                'category' => 'Hair Braiding',
                'active' => true
            ],
            [
                'name' => 'Butterfly Locs (Waist Length)',
                'description' => 'Stunning butterfly locs extending to waist length. Creates a sophisticated, eye-catching look with maximum elegance.',
                'price' => 170.00,
                'duration' => 360,
                'category' => 'Hair Braiding',
                'active' => true
            ],

            // CHILDREN'S SERVICES
            [
                'name' => 'Children\'s Styles (Under 10)',
                'description' => 'Specialized braiding services for children under 10. Gentle, age-appropriate styles with shorter installation time.',
                'price' => 90.00,
                'duration' => 180,
                'category' => 'Hair Braiding',
                'active' => true
            ],

            // MAINTENANCE SERVICES
            [
                'name' => 'Take Down Service',
                'description' => 'Professional removal of braids, twists, or locs. Includes detangling and hair preparation for your next style.',
                'price' => 40.00,
                'duration' => 60,
                'category' => 'Hair Treatment',
                'active' => true
            ],

            // STYLING SERVICES
            [
                'name' => 'Wash and Curl',
                'description' => 'A refreshing wash followed by a professional curl styling. Perfect for maintaining natural curl patterns.',
                'price' => 50.00,
                'duration' => 60,
                'category' => 'Hair Styling',
                'active' => true
            ],
            [
                'name' => 'Rod Set',
                'description' => 'Setting the hair on rods to create uniform curls. Classic styling technique for defined, long-lasting curls.',
                'price' => 50.00,
                'duration' => 90,
                'category' => 'Hair Styling',
                'active' => true
            ],

            // WEAVE SERVICES
            [
                'name' => 'Full Sew-in Weave',
                'description' => 'A full head of extensions expertly sewn in. Complete coverage for maximum versatility and styling options.',
                'price' => 150.00,
                'duration' => 180,
                'category' => 'Hair Extensions',
                'active' => true
            ],
            [
                'name' => 'Partial Sew-in Weave',
                'description' => 'Adding extensions to a portion of your hair for volume or length. Natural blend with your own hair left out.',
                'price' => 90.00,
                'duration' => 120,
                'category' => 'Hair Extensions',
                'active' => true
            ],

            // TREATMENT SERVICES
            [
                'name' => 'Moisturizing Treatment',
                'description' => 'A deep conditioning treatment to restore moisture and shine. Essential for maintaining healthy, hydrated hair.',
                'price' => 50.00,
                'duration' => 45,
                'category' => 'Hair Treatment',
                'active' => true
            ],
            [
                'name' => 'Protein Reconstructor',
                'description' => 'A treatment to strengthen and repair damaged hair. Rebuilds hair structure for improved strength and elasticity.',
                'price' => 50.00,
                'duration' => 60,
                'category' => 'Hair Treatment',
                'active' => true
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
