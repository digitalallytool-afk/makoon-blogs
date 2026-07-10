<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use App\Models\StoryCategory;
use App\Models\Story;
use App\Models\Printable;
use App\Models\SessionCategory;
use App\Models\VideoSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Main Categories
        $schoolLife = Category::updateOrCreate(
            ['slug' => 'school-life'],
            ['name' => 'School Life', 'description' => 'First days, classroom confidence, routines, and friendships.']
        );

        $familySupport = Category::updateOrCreate(
            ['slug' => 'family-support'],
            ['name' => 'Family Support', 'description' => 'Routines, feelings, behaviour, and calmer transitions.']
        );

        $atHome = Category::updateOrCreate(
            ['slug' => 'at-home'],
            ['name' => 'At Home', 'description' => 'Simple play ideas, reading, art, and movement at home.']
        );

        // 2. Create Sub Categories
        $preschool = Category::updateOrCreate(
            ['slug' => 'preschool'],
            ['name' => 'Preschool', 'parent_id' => $schoolLife->id, 'description' => 'First days, classroom confidence, routines, and friendships.']
        );

        $daycare = Category::updateOrCreate(
            ['slug' => 'daycare'],
            ['name' => 'Daycare', 'parent_id' => $schoolLife->id, 'description' => 'Meals, naps, care, comfort, and longer school days.']
        );

        $play = Category::updateOrCreate(
            ['slug' => 'play'],
            ['name' => 'Play', 'parent_id' => $schoolLife->id, 'description' => 'What children learn when they play and repeat games.']
        );

        $food = Category::updateOrCreate(
            ['slug' => 'food'],
            ['name' => 'Food', 'parent_id' => $schoolLife->id, 'description' => 'Familiar, easy-to-eat foods that support comfort.']
        );

        $parenting = Category::updateOrCreate(
            ['slug' => 'parenting'],
            ['name' => 'Parenting', 'parent_id' => $familySupport->id, 'description' => 'Routines, feelings, behaviour, and calmer transitions.']
        );

        $activities = Category::updateOrCreate(
            ['slug' => 'activities'],
            ['name' => 'Activities', 'parent_id' => $atHome->id, 'description' => 'Simple play ideas, reading, art, and movement at home.']
        );

        // 3. Create Authors
        $authorsData = [
            'Mira Sharma' => [
                'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80',
                'description' => 'Mira Sharma is a child development specialist with 10 years of experience helping children adapt to preschool life.'
            ],
            'Neha Batra' => [
                'image' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=120&q=80',
                'description' => 'Neha Batra focuses on family transitions, school mornings, and helping parents build calm home environments.'
            ],
            'Riya Sen' => [
                'image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=120&q=80',
                'description' => 'Riya Sen specializes in play-based learning, childhood creativity, and emotional support routines.'
            ],
            'Aarav Mehta' => [
                'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=120&q=80',
                'description' => 'Aarav Mehta coordinates daycare programs, optimizing sleep schedules, meals, and comforting care routines.'
            ],
            'Sana Kapoor' => [
                'image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80',
                'description' => 'Sana writes about food, comfort routines, and the small practical choices that help preschool children settle into school life with more ease.'
            ],
            'Activity team' => [
                'image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=120&q=80',
                'description' => 'Our internal Activity Team compiles curriculum extensions, crafts, and interactive home games.'
            ],
            'Kabir Anand' => [
                'image' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=120&q=80',
                'description' => 'Kabir Anand focuses on early literacy, shared reading rituals, and building lifelong learning habits.'
            ],
        ];

        $authors = [];
        foreach ($authorsData as $name => $info) {
            $authors[$name] = Author::updateOrCreate(
                ['name' => $name],
                [
                    'image' => $info['image'],
                    'description' => $info['description']
                ]
            );
        }

        // 4. Create Posts
        $postsData = [
            // Preschool Life
            [
                'title' => 'A gentle guide to your child’s first week at preschool',
                'slug' => 'a-gentle-guide-to-your-childs-first-week-at-preschool',
                'content' => 'Starting preschool is a big milestone. Set up a simple goodbye ritual. Keep drop-offs short but loving. A quick hug, a reassuring word, and a confident wave help more than long goodbyes. When children see their parents leave calmly, they feel the classroom is a safe space.',
                'excerpt' => 'Small routines that help children feel calm, known, and ready to join the classroom.',
                'featured_image' => 'media-one',
                'category_id' => $preschool->id,
                'author_id' => $authors['Mira Sharma']->id,
                'view_count' => 2400,
                'created_at' => '2026-06-12 09:00:00'
            ],
            [
                'title' => 'What helps children feel at home in a new classroom',
                'slug' => 'what-helps-children-feel-at-home-in-a-new-classroom',
                'content' => 'Trust is built through patterns. Familiar greetings, having a dedicated cubby or drawer for their bag, and small daily jobs like helping water the plants make children feel like they belong. Consistent daily schedules provide a framework of safety.',
                'excerpt' => 'How familiar greetings, simple roles, and repeated moments build trust.',
                'featured_image' => 'media-two',
                'category_id' => $preschool->id,
                'author_id' => $authors['Neha Batra']->id,
                'view_count' => 1280,
                'created_at' => '2026-06-08 10:00:00'
            ],
            [
                'title' => 'What children learn when they repeat the same game',
                'slug' => 'what-children-learn-when-they-repeat-the-same-game',
                'content' => 'Repetition is how young brains build connections. Building the same block tower or reading the same story helps children master skills, predict outcomes, and feel confident in their abilities. It is a sign of deep learning, not boredom.',
                'excerpt' => 'A calmer way to understand repetition, confidence, imagination, and problem solving.',
                'featured_image' => 'media-three',
                'category_id' => $play->id,
                'author_id' => $authors['Riya Sen']->id,
                'view_count' => 1200,
                'created_at' => '2026-05-28 11:00:00'
            ],

            // Daycare Routines
            [
                'title' => 'How thoughtful daycare routines help children feel secure',
                'slug' => 'how-thoughtful-daycare-routines-help-children-feel-secure',
                'content' => 'Structured care routines help children predict their day. Knowing that naptime always follows lunch and storytime always precedes outdoor play gives children a sense of control and reduces separation anxiety. Daily rhythms form the backbone of security.',
                'excerpt' => 'Why repeated rhythms make the day easier for children and more predictable for parents.',
                'featured_image' => 'media-four',
                'category_id' => $daycare->id,
                'author_id' => $authors['Aarav Mehta']->id,
                'view_count' => 1800,
                'created_at' => '2026-06-10 14:00:00'
            ],
            [
                'title' => 'Simple lunchbox ideas for the first month of school',
                'slug' => 'simple-lunchbox-ideas-for-the-first-month-of-school',
                'content' => 'Comfort is key when starting school. Pack familiar, easy-to-manage foods. A favorite fruit, pre-cut vegetables, or simple rolls are comforting. Avoid introducing new or hard-to-open foods during the transition phase, as lunch should feel successful and reassuring.',
                'excerpt' => 'Familiar, easy-to-eat foods that support comfort while children settle.',
                'featured_image' => 'media-one',
                'category_id' => $food->id,
                'author_id' => $authors['Sana Kapoor']->id,
                'view_count' => 1500,
                'created_at' => '2026-06-04 12:00:00'
            ],
            [
                'title' => 'What children need from a long daycare day',
                'slug' => 'what-children-need-from-a-long-daycare-day',
                'content' => 'Longer school days require balanced energy. Ensuring children have quiet rest times, nutritious snacks, unstructured play, and warm interactions with care educators helps maintain their emotional balance and overall well-being throughout the day.',
                'excerpt' => 'Rest, food, play, and familiar adults all matter more than a perfect schedule.',
                'featured_image' => 'media-two',
                'category_id' => $daycare->id,
                'author_id' => $authors['Aarav Mehta']->id,
                'view_count' => 814,
                'created_at' => '2026-05-22 13:00:00'
            ],

            // Parenting Guides
            [
                'title' => 'A calmer preschool morning starts the night before',
                'slug' => 'a-calmer-preschool-morning-starts-the-night-before',
                'content' => 'Mornings are easily rushed. Prepare clothes, pack lunchboxes, and lay out shoes the night before. Waking up 15 minutes earlier can create a peaceful, unhurried space where you can share breakfast with your child and make leaving the house a pleasant experience.',
                'excerpt' => 'Tiny choices that make leaving home feel less rushed.',
                'featured_image' => 'media-three',
                'category_id' => $parenting->id,
                'author_id' => $authors['Neha Batra']->id,
                'view_count' => 946,
                'created_at' => '2026-05-18 08:30:00'
            ],
            [
                'title' => 'When children do not have the words yet',
                'slug' => 'when-children-do-not-have-the-words-yet',
                'content' => 'Behavior is communication. When young children are tired or frustrated, they may cry or act out instead of speaking. Offering simple, reassuring phrases like "I see you are tired" or "I am here" helps validate their feelings and teaches them emotional regulation.',
                'excerpt' => 'Simple phrases for tired evenings, drop-offs, and big feelings.',
                'featured_image' => 'media-four',
                'category_id' => $parenting->id,
                'author_id' => $authors['Mira Sharma']->id,
                'view_count' => 604,
                'created_at' => '2026-05-10 16:00:00'
            ],
            [
                'title' => 'Helping children handle big feelings in small steps',
                'slug' => 'helping-children-handle-big-feelings-in-small-steps',
                'content' => 'Dealing with overwhelming emotions is a learned skill. Break down situations into manageable steps. Provide a quiet space for children to calm down, use soft tones, and offer comfort. Over time, these small actions build emotional resilience and confidence.',
                'excerpt' => 'Gentle language and routines parents can use when children feel overwhelmed.',
                'featured_image' => 'media-one',
                'category_id' => $parenting->id,
                'author_id' => $authors['Riya Sen']->id,
                'view_count' => 690,
                'created_at' => '2026-04-26 15:00:00'
            ],

            // Home Activities
            [
                'title' => 'Classroom activities that work at the kitchen table',
                'slug' => 'classroom-activities-that-work-at-the-kitchen-table',
                'content' => 'Learning happens everywhere. Simple sensory activities like playing with dough, sorting beans, or pouring water into containers help build fine motor skills and hand-eye coordination. These preschool concepts can easily be integrated into kitchen routines.',
                'excerpt' => 'Short activities that bring preschool-style learning into ordinary home routines.',
                'featured_image' => 'media-two',
                'category_id' => $activities->id,
                'author_id' => $authors['Activity team']->id,
                'view_count' => 629,
                'created_at' => '2026-06-02 11:30:00'
            ],
            [
                'title' => 'Weekend play that does not need a shopping list',
                'slug' => 'weekend-play-that-does-not-need-a-shopping-list',
                'content' => 'Kids do not need expensive toys. Cardboard boxes, old cushions, plastic cups, and safety scissors can provide hours of creative exploration. Let your child lead the play and watch how their imagination turns simple items into amazing adventures.',
                'excerpt' => 'Use what is already at home and let children lead a little.',
                'featured_image' => 'media-three',
                'category_id' => $activities->id,
                'author_id' => $authors['Riya Sen']->id,
                'view_count' => 732,
                'created_at' => '2026-05-16 10:00:00'
            ],
            [
                'title' => 'The reading habit that matters most is showing up',
                'slug' => 'the-reading-habit-that-matters-most-is-showing-up',
                'content' => 'Shared reading builds vocabulary and connection. Do not worry about reading the entire book or doing it perfectly. Just pointing at pictures, naming characters, and spending 5-10 quiet minutes looking at books together creates a positive association with reading.',
                'excerpt' => 'A few minutes, repeated often, can do more than a perfect setup.',
                'featured_image' => 'media-four',
                'category_id' => $activities->id,
                'author_id' => $authors['Kabir Anand']->id,
                'view_count' => 689,
                'created_at' => '2026-05-02 14:00:00'
            ],
        ];

        foreach ($postsData as $post) {
            Post::updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'title' => $post['title'],
                    'content' => $post['content'],
                    'excerpt' => $post['excerpt'],
                    'featured_image' => $post['featured_image'],
                    'category_id' => $post['category_id'],
                    'author_id' => $post['author_id'],
                    'view_count' => $post['view_count'],
                    'status' => 'published',
                    'created_at' => $post['created_at'],
                    'updated_at' => $post['created_at']
                ]
            );
        }

        // 5. Create Story Categories & Stories
        $storyCat1 = StoryCategory::updateOrCreate(['slug' => 'classroom-story'], ['name' => 'Classroom story', 'description' => 'Stories from classrooms.']);
        $storyCat2 = StoryCategory::updateOrCreate(['slug' => 'preschool-life'], ['name' => 'Preschool life', 'description' => 'Stories from preschool life.']);
        $storyCat3 = StoryCategory::updateOrCreate(['slug' => 'art-table'], ['name' => 'Art table', 'description' => 'Stories from art table.']);
        $storyCat4 = StoryCategory::updateOrCreate(['slug' => 'morning-routine'], ['name' => 'Morning routine', 'description' => 'Stories from morning routine.']);

        $storiesData = [
            [
                'title' => 'The day a quiet child became the classroom helper',
                'slug' => 'the-day-a-quiet-child-became-the-classroom-helper',
                'content' => 'A gentle story about confidence, small responsibilities, and how children begin to feel known at school.',
                'excerpt' => 'A gentle story about confidence, small responsibilities, and how children begin to feel known at school.',
                'featured_image' => 'story-one',
                'story_category_id' => $storyCat1->id,
                'author_id' => $authors['Mira Sharma']->id,
                'view_count' => 1200,
            ],
            [
                'title' => 'A lunchbox, two friends, and one shared smile',
                'slug' => 'a-lunchbox-two-friends-and-one-shared-smile',
                'content' => 'How familiar food helped a child feel at home and gave two children a reason to talk.',
                'excerpt' => 'How familiar food helped a child feel at home and gave two children a reason to talk.',
                'featured_image' => 'story-two',
                'story_category_id' => $storyCat2->id,
                'author_id' => $authors['Mira Sharma']->id,
                'view_count' => 950,
            ],
            [
                'title' => 'What children say when paint gets messy',
                'slug' => 'what-children-say-when-paint-gets-messy',
                'content' => 'Little words teachers hear when children use color, texture, and play to explain their world.',
                'excerpt' => 'Little words teachers hear when children use color, texture, and play to explain their world.',
                'featured_image' => 'story-three',
                'story_category_id' => $storyCat3->id,
                'author_id' => $authors['Riya Sen']->id,
                'view_count' => 840,
            ],
            [
                'title' => 'Why one song can settle a whole room',
                'slug' => 'why-one-song-can-settle-a-whole-room',
                'content' => 'The small ritual that helped children start the day with rhythm, memory, and a shared voice.',
                'excerpt' => 'The small ritual that helped children start the day with rhythm, memory, and a shared voice.',
                'featured_image' => 'story-four',
                'story_category_id' => $storyCat4->id,
                'author_id' => $authors['Neha Batra']->id,
                'view_count' => 1100,
            ]
        ];

        foreach ($storiesData as $story) {
            Story::updateOrCreate(
                ['slug' => $story['slug']],
                [
                    'title' => $story['title'],
                    'content' => $story['content'],
                    'excerpt' => $story['excerpt'],
                    'featured_image' => $story['featured_image'],
                    'story_category_id' => $story['story_category_id'],
                    'author_id' => $story['author_id'],
                    'view_count' => $story['view_count'],
                    'status' => 'published'
                ]
            );
        }

        // 6. Create Printables
        $printablesData = [
            [
                'name' => 'Halloween 2024',
                'slug' => 'halloween-2024',
                'description' => 'Spooky coloring pages with friendly shapes for preschool hands.',
                'image' => 'printable-halloween',
                'file_path' => 'uploads/printables/files/halloween-2024.pdf',
                'file_name' => 'Halloween 2024.pdf',
                'file_size' => 102400,
                'download_count' => 340,
            ],
            [
                'name' => 'Happy Holidays',
                'slug' => 'happy-holidays',
                'description' => 'Warm holiday sheets for coloring, gifting, and family time.',
                'image' => 'printable-holidays',
                'file_path' => 'uploads/printables/files/happy-holidays.pdf',
                'file_name' => 'Happy Holidays.pdf',
                'file_size' => 204800,
                'download_count' => 180,
            ],
            [
                'name' => 'Halloween 2022',
                'slug' => 'halloween-2022',
                'description' => 'Simple costume, pumpkin, and pattern pages for little artists.',
                'image' => 'printable-halloween-two',
                'file_path' => 'uploads/printables/files/halloween-2022.pdf',
                'file_name' => 'Halloween 2022.pdf',
                'file_size' => 98200,
                'download_count' => 210,
            ],
            [
                'name' => 'Space 2022',
                'slug' => 'space-2022',
                'description' => 'Planets, rockets, stars, and imagination-ready drawing pages.',
                'image' => 'printable-space',
                'file_path' => 'uploads/printables/files/space-2022.pdf',
                'file_name' => 'Space 2022.pdf',
                'file_size' => 153600,
                'download_count' => 450,
            ],
        ];

        foreach ($printablesData as $printable) {
            Printable::updateOrCreate(
                ['slug' => $printable['slug']],
                [
                    'name' => $printable['name'],
                    'description' => $printable['description'],
                    'image' => $printable['image'],
                    'file_path' => $printable['file_path'],
                    'file_name' => $printable['file_name'],
                    'file_size' => $printable['file_size'],
                    'download_count' => $printable['download_count'],
                    'status' => 'published'
                ]
            );
        }

        // 7. Create Session Categories & Video Sessions
        $sessCat1 = SessionCategory::updateOrCreate(['slug' => 'child-development'], ['name' => 'Child development']);
        $sessCat2 = SessionCategory::updateOrCreate(['slug' => 'food-and-health'], ['name' => 'Food and health']);
        $sessCat3 = SessionCategory::updateOrCreate(['slug' => 'growth-check'], ['name' => 'Growth check']);

        $sessionsData = [
            [
                'title' => 'Building confidence through the right everyday experiences',
                'slug' => 'building-confidence-through-the-right-everyday-experiences',
                'description' => 'How simple exposure at home and school helps children try, observe, and grow with more confidence.',
                'video_url' => 'https://www.youtube.com/embed/Il7t8iqOQ7M',
                'image' => 'session-one',
                'session_category_id' => $sessCat1->id,
            ],
            [
                'title' => 'Reading food choices with a calmer parent mindset',
                'slug' => 'reading-food-choices-with-a-calmer-parent-mindset',
                'description' => 'A practical way to look at snacks, labels, and lunchbox habits without making food stressful.',
                'video_url' => 'https://www.youtube.com/embed/Il7t8iqOQ7M',
                'image' => 'session-two',
                'session_category_id' => $sessCat2->id,
            ],
            [
                'title' => 'Small signs that show your child is growing well',
                'slug' => 'small-signs-that-show-your-child-is-growing-well',
                'description' => 'What teachers notice in play, language, routines, friendships, and independence over time.',
                'video_url' => 'https://www.youtube.com/embed/Il7t8iqOQ7M',
                'image' => 'session-three',
                'session_category_id' => $sessCat3->id,
            ]
        ];

        foreach ($sessionsData as $sess) {
            VideoSession::updateOrCreate(
                ['slug' => $sess['slug']],
                [
                    'title' => $sess['title'],
                    'description' => $sess['description'],
                    'video_url' => $sess['video_url'],
                    'image' => $sess['image'],
                    'session_category_id' => $sess['session_category_id'],
                    'status' => 'published'
                ]
            );
        }
    }
}
