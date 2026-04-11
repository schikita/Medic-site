<?php

declare(strict_types=1);

require_once __DIR__ . '/theme_assets.php';

/**
 * Дефолтные блоки страниц 1 (Home), 2 (Professionals), 3 (Institutions), 4 (Blog), 5 (Partners) по ТЗ.
 * Картинки: при наличии файлов в public/assets/img/figma/{страница}/ подключаются они, иначе — picsum (как раньше).
 * Список имён файлов — public/assets/img/figma/FILES.txt
 */
function xr_default_hubspot(): array
{
    return [
        'whitepaper_url' => '',
        'demo_url' => '',
    ];
}

function xr_default_nav(): array
{
    return [
        'logo_alt' => 'XR Doctor',
        'items' => [
            ['label' => 'XR Doctor Platform', 'href' => '/', 'page' => 'home'],
            ['label' => 'for Professionals', 'href' => '/professionals.php', 'page' => 'professionals'],
            ['label' => 'for Instututions', 'href' => '/institutions.php', 'page' => 'institutions'],
            ['label' => 'Blog', 'href' => '/blog.php', 'page' => 'blog'],
            ['label' => 'Partner with Us', 'href' => '/partners.php', 'page' => 'partners'],
        ],
        'cta_outline' => ['label' => 'White Paper', 'href' => '#hubspot-whitepaper'],
        'cta_gradient' => ['label' => 'Request Demo', 'href' => '#hubspot-demo'],
    ];
}

function xr_default_home_blocks(): array
{
    $img = static function (string $suffix, int $w, int $h): string {
        return xr_figma_asset('home', 'home-' . $suffix, 'xrhome' . $suffix, $w, $h);
    };

    return [
        [
            'type' => 'hero_fullscreen',
            'id' => 'block-1-1',
            'props' => [
                'poster' => xr_figma_asset('home', 'home-block-1-1-poster', 'xrhero1', 1440, 810),
                'video_mp4' => '',
                'video_webm' => '',
                'overlay_note' => '',
                'overlay_lines' => ['Автоплей', 'Зацикленное видео'],
            ],
        ],
        [
            'type' => 'intro_gradient',
            'id' => 'institutions',
            'props' => [
                'eyebrow' => 'AR Glasses enhanced by AI are Ultimate Game-Changers',
                'headline_line1' => 'XR Doctor brings to life a Next-GeN XR Platform',
                'headline_line2' => 'seamlessly blending Digital & Physical Worlds',
                'body' => 'XR Doctor enables medical professionals and institutions to access breakthrough holographic technology and adaptive AI support. See inside the body and explore in true depth like never before. Instantly connect and collaborate in a shared space – as if side by side – from anywhere, at any time, with anyone across the world. The new standard in daily work and education. One Platform. Every Role.',
            ],
        ],
        [
            'type' => 'wave_slider',
            'id' => 'block-1-2-slider',
            'props' => [
                'headline' => 'Discover Your Hologram Edge',
                'badges' => ['XR Doctor', 'Exclusive'],
                'slides' => [
                    [
                        'image' => xr_figma_asset('home', 'home_a', 'xrhomea', 1440, 720),
                        'subheadline' => 'Visualize Patient Data - in Live Hologram',
                    ],
                    [
                        'image' => xr_figma_asset('home', 'home_b', 'xrhomeb', 1440, 720),
                        'subheadline' => 'Explore the Human Body from the Inside',
                    ],
                    [
                        'image' => xr_figma_asset('home', 'home_c', 'xrhomec', 1440, 720),
                        'subheadline' => 'Interact with Immersive Real-Life Scenarios',
                    ],
                ],
                'interval_ms' => 5000,
            ],
        ],
        [
            'type' => 'layered_star',
            'id' => 'block-1-3',
            'props' => [
                'title' => "Unlock XR Doctor's Power",
                'title_line2' => 'for Scalable Impact',
                'subtitle' => 'Solves complex real-world challenges with simple & effective solutions - seamlessly integrated into daily workflows.',
                'base_color' => '#151a22',
            ],
        ],
        [
            'type' => 'tabs_youtube_loop',
            'id' => 'block-1-4-5',
            'props' => [
                'layout' => 'split',
                'heading' => 'Turn Challenges into Solutions',
                'subheading' => 'Step into a New Medical Reality',
                'heading_size' => 'lg',
                'tabs' => [
                    [
                        'label' => 'Hologram Image',
                        'badge' => 'Up - Level',
                        'panel_title' => 'True-to-Life Holograms of Patient Data Reveal Insight and Clarity',
                        'body' => "Hologram Images solve one of the most urgent problems in a doctor's work and study: the inability to clearly visualize and fully understand patient data in its true volumetric shape, as it exists inside the body. The cost of error is measured in a patient's health and life. Even advanced tools struggle to recreate accurate volumetric images and precisely map spatial relationships. XR Doctor addresses these challenges by providing doctors with layered, scalable, mapped, true-to-life holograms for accurate diagnostics and treatment, building stronger patient trust, offering students insights, empowering educators with immersive training tools.",
                        'overlay_line1' => 'SEE',
                        'overlay_line2' => 'Your Edge Appear',
                        'mode' => 'youtube_click',
                        'poster' => $img('d', 800, 500),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'play_label' => '',
                    ],
                    [
                        'label' => 'Digital Patient',
                        'badge' => 'Up - Level',
                        'panel_title' => 'Digital Patient — Practice on Realistic Virtual Cases',
                        'body' => "Digital Patient expands and enhances unique capabilities of hologram images of inner human systems by integrating them into interactive layered body holograms. XR Doctor solves a critical challenge: offering a lifelike view inside the body — letting you see what's usually hidden, beyond what's visible on a real patient. Doctors, students and educators gain hands-on holographic tools that bridge the gap between isolated data and full-body understanding — letting switch layers and focus on each system. Lifelike case simulations help prepare for real-world medical challenges without patient physical presence with a clear view inside the body.",
                        'overlay_line1' => 'SEE',
                        'overlay_line2' => 'Your Edge Appear',
                        'mode' => 'youtube_click',
                        'poster' => $img('d', 800, 500),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'play_label' => '',
                    ],
                    [
                        'label' => 'Case Simulation',
                        'badge' => 'Up - Level',
                        'panel_title' => 'Case Simulation — From Briefing to Decision in One Space',
                        'body' => "Case Simulations harness the power of Hologram Images and Digital Patient to create customized, immersive scenarios based on real or modeled clinical cases, enriching libraries of tools and equipment. XR Doctor takes teams into a new dimension of medical training to learn, test and master clinical skills by seeing and understanding what happens inside the human body in a zero-risk environment. Set up interactive holographic training scenarios in seconds — anywhere in the world, without extra costs or physical equipment. This helps solve one of the world's greatest healthcare problems: how to train more doctors faster, better, with less cost.",
                        'overlay_line1' => 'SEE',
                        'overlay_line2' => 'Your Edge Appear',
                        'mode' => 'youtube_click',
                        'poster' => $img('d', 800, 500),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'play_label' => '',
                    ],
                ],
            ],
        ],
        [
            'type' => 'video_freeze_section',
            'id' => 'block-1-6',
            'props' => [
                'heading' => 'Unite the Global Medical World',
                'heading_line2' => 'Through One Scalable Platform',
                'intro' => 'XR Doctor Platform. More than a Tool. A Next-Gen Medical Infrastructure.',
                'mp4' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'poster' => $img('g', 1440, 800),
                'caption' => 'Intro sequence — freezes on last frame',
            ],
        ],
        [
            'type' => 'layered_star',
            'id' => 'block-1-6-star',
            'props' => [
                'title' => "Unlock XR Doctor's Power",
                'title_line2' => 'for Scalable Impact',
                'subtitle' => 'Solves complex real-world challenges with simple & effective solutions - seamlessly integrated into daily workflows.',
                'base_color' => '#151a22',
                'down_wave' => false,
            ],
        ],
        [
            'type' => 'product_tabs',
            'id' => 'block-1-7-8',
            'props' => [
                'layout' => 'slider',
                'showcase_badge' => 'Explore XR Doctor Platform',
                'showcase_heading' => "Engage &\nApply for\nNext-GeN",
                'showcase_items' => [
                    ['label' => 'Next-GeN Standard'],
                    ['label' => 'High-Impact Products', 'underline' => true],
                    ['label' => 'Built-in AI Support'],
                ],
                'tabs' => ['XR Doctor Assistant', 'XR Doctor Training', 'XR Doctor Collaboration'],
                'active_tab' => 0,
                'panels' => [
                    [
                        'title' => 'XR Doctor Assistant',
                        'lead' => 'An instantly deployed workspace for daily medical tasks, education and global collaboration.',
                        'body' => 'Upload CT scans, clinical or simulated 3D models, and instantly view live interactive holograms in AR/VR glasses.',
                        'emphasis' => 'This isn’t just a visual — it’s an interactive holographic tool that merges with the real world.',
                        'sidebar' => 'Interactive, scalable, mapped and shared holograms across specializations.',
                        'features' => [['label' => 'Patient Care'], ['label' => 'Team Work'], ['label' => 'Telementoring']],
                        'card_features' => [
                            ['label' => 'One tool – for core medical tasks'],
                            ['label' => 'Interact with medical holograms'],
                            ['label' => 'Work together from any location'],
                            ['label' => 'Interactive hologram telemedicine'],
                        ],
                        'card_image' => $img('d', 800, 500),
                        'bottom_title' => 'Inside XR Doctor - uncover the full system',
                        'bottom_link' => ['label' => 'how it defines the Next-GeN Medical Standard', 'href' => '#block-1-9'],
                        'poster' => $img('d', 800, 500),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'video_label' => "See\nXR Doctor Platform\nin Action",
                    ],
                    [
                        'title' => 'XR Doctor Training',
                        'lead' => 'AR glasses become your portable training center — protocol scenarios, case video, anonymized data in one spatial environment.',
                        'body' => 'Practice procedures, test decisions, build muscle memory through holographic experience.',
                        'emphasis' => 'Fully immersive training for real clinical readiness.',
                        'sidebar' => 'Case simulation at full scale, anywhere in the world.',
                        'features' => [['label' => 'Case Simulation'], ['label' => 'Test Skills'], ['label' => 'Hologram Library']],
                        'card_features' => [
                            ['label' => 'Launch instantly – in any place'],
                            ['label' => 'Train in lifelike environment'],
                            ['label' => 'Master skills until ready'],
                            ['label' => 'Available without a teacher'],
                        ],
                        'card_image' => $img('e', 800, 500),
                        'bottom_title' => 'Inside XR Doctor - uncover the full system',
                        'bottom_link' => ['label' => 'how it defines the Next-GeN Medical Standard', 'href' => '#'],
                        'poster' => $img('e', 800, 500),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'video_label' => "See\nXR Doctor Platform\nin Action",
                    ],
                    [
                        'title' => 'XR Doctor Collaboration',
                        'lead' => 'Stand around the same case remotely or on-site — see what colleagues see, in real time.',
                        'body' => 'Shared holograms for consultation, education, and research workflows.',
                        'emphasis' => 'A new shared space for clinical thinking and action.',
                        'sidebar' => 'Mapped layers, scalable detail, secure sharing.',
                        'features' => [['label' => 'Telementoring'], ['label' => 'Team Work'], ['label' => 'Patient Care']],
                        'card_features' => [
                            ['label' => 'Connect teams globally'],
                            ['label' => 'Share holograms in real time'],
                            ['label' => 'Annotate & guide remotely'],
                            ['label' => 'Secure & GDPR compliant'],
                        ],
                        'card_image' => $img('f', 800, 500),
                        'feature_grid' => [
                            [
                                'icon_color' => 'cyan',
                                'title' => 'XR Doctor AI Suite',
                                'body' => 'AI suite designed to work like you do — providing insights, adapting to your daily tasks and helping you act faster, safer & with more clarity',
                            ],
                            [
                                'icon_color' => 'cyan',
                                'title' => 'AI Clinical Assistant',
                                'body' => 'AI voice delivers early insights in advance and empowers precise real-time decisions during hologram work',
                            ],
                            [
                                'icon_color' => 'pink',
                                'title' => 'AI Training Coach',
                                'body' => 'AI voice coach helps develop clinical skills in holographic XR and guides step-by-step to fix mistakes and grow at your pace',
                            ],
                            [
                                'icon_color' => 'purple',
                                'title' => 'AI Teamwork Mentor',
                                'body' => 'AI voice mentor enabling shared understanding, gathering team insights and guiding decisions before, during and after meetings',
                            ],
                        ],
                        'bottom_title' => 'Inside XR Doctor - uncover the full system',
                        'bottom_link' => ['label' => 'how it defines the Next-GeN Medical Standard', 'href' => '#'],
                        'poster' => $img('f', 800, 500),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'video_label' => "See\nXR Doctor Platform\nin Action",
                    ],
                ],
            ],
        ],
        [
            'type' => 'product_detail_tabs',
            'id' => 'block-1-8-detail',
            'props' => [
                'tabs' => [
                    [
                        'label' => 'XR Doctor Assistant',
                        'title' => 'XR Doctor Assistant',
                        'body' => [
                            'XR Doctor Assistant is an instantly deployed workspace for daily medical tasks, education and global collaboration. Upload CT scans, clinical or simulated 3D models, and instantly view live interactive holograms in AR/VR glasses — supporting real-world tasks. While treating a patient, studying anatomy, exploring clinical cases, educating students, consulting colleagues, researching surgical approaches, or presenting at a workshop or conference — interact with mapped, layered, scalable and shared holograms. It helps you make faster, more precise decisions, reduce medical risks, improve patient and learning outcomes, deepen understanding. Share holograms remotely or on-site for any collaborative purpose.',
                            '**What was never possible before now becomes part of your everyday reality.** For the first time, you can stand around the same case, explore the same data in full detail and real time — from anywhere, see exactly what your colleagues see — as if you were in the same room. This is the new shared space for clinical thinking, learning and action — powered by XR Doctor Assistant.',
                            '*This isn\'t just a visual — it\'s an interactive holographic tool that merges with the real world.*',
                        ],
                        'sub_items' => [
                            ['icon' => '⊙', 'label' => 'Patient Care', 'content' => 'XR Doctor sets a new clinical standard for diagnostics and treatment — with interactive, scalable, mapped and shared holograms across all medical specializations.'],
                            ['icon' => '⚙', 'label' => 'Team Work', 'content' => 'Work together seamlessly — in perfect sync — through XR Doctor spatial collaboration and global access, as if in the same room, even when not.'],
                            ['icon' => '🌐', 'label' => 'Telementoring', 'content' => 'XR Doctor Tele-Mentoring takes telemedicine beyond screens — into shared holographic presence with real-time case interaction and guided precision, used from OR to classroom.'],
                        ],
                    ],
                    [
                        'label' => 'XR Doctor Training',
                        'title' => 'XR Doctor Training',
                        'body' => [
                            '**What used to require years of shadowing and rare case access is now instantly available — personalized, repeatable, clinically accurate.**',
                            'XR Doctor Training turns AR glasses into your portable training center. Anytime, anywhere, you can step into a realistic medical environment — with holograms of patients, organs and tools. Users follow protocol-based scenarios, review case videos, study original (anonymized) patient data, and access supporting materials — all inside one spatial environment. When working with multiple cases, simulations can be shared and reviewed collaboratively in real time.',
                            'Whether you\'re learning alone or with others, you can practice procedures, test your decisions and build muscle memory through holographic experience. Follow senior colleagues in real-time streams and perform the same actions on your digital patient — synchronized, interactive, hands-on. It\'s direct, dynamic learning that prepares you for real-world performance.',
                            '*This is not just a simulation — it\'s a fully immersive training tool designed for real clinical readiness.*',
                        ],
                        'sub_items' => [
                            ['icon' => '⊙', 'label' => 'Hologram Library', 'content' => 'XR Doctor Hologram Library gives instant access to holographic medical environments aligned with real-world space — ready to deploy in any location, at any scale.'],
                            ['icon' => '⚙', 'label' => 'Case Simulation', 'content' => 'XR Doctor Case Simulation delivers hands-on Medical XR Training to any place in the world — instantly, at full scale, with no physical setup and no instructors.'],
                            ['icon' => '🌐', 'label' => 'Test and Master Skills', 'content' => 'XR Doctor Inside-View Testing and Skill Mastery adds a new layer of clinical immersion — clearly demonstrating how each chosen answer or performed action affects the visible response on the digital manikin.'],
                        ],
                    ],
                    [
                        'label' => 'XR Doctor AI Suite',
                        'title' => 'XR Doctor AI Suite',
                        'body' => [
                            '**The built-in AI Suite enhances the XR Doctor Next-Gen Standard for Daily Work and Education**',
                            'AI Suite is a unified voice-powered system that prepares clinical cases in advance, detects key issues and supports professionals during live review. Built into the platform and always on, it\'s designed to assist users in real clinical, educational and collaborative scenarios.',
                            'The system works by analyzing all uploaded data — from scans and notes to structured findings — and generating relevant insights before start. It highlights diagnostic risks, compares patterns and helps users focus on what matters. Whether reviewing a case, running a simulation, or guiding a team, the AI is ready the moment they are. Instead of one assistant trying to do everything, AI Suite includes three distinct roles — each tailored to a core function of daily medical work: clinical reasoning, real-time collaboration, medical training. Each voice role is activated naturally during workflow, helping move faster, think sharper and collaborate better — without any extra setup.',
                            '*AI Suite adds a powerful cognitive layer to XR Doctor — elevating the platform into a clinically intelligent environment*',
                        ],
                        'sub_items' => [
                            ['icon' => '⊙', 'label' => 'AI Clinical Assistant', 'content' => 'AI Clinical Assistant is an on-demand proactive clinical co-pilot that makes every case review in XR Doctor smarter and more efficient — through AI pre-case brief and intelligent real-time voice support.'],
                            ['icon' => '⚙', 'label' => 'AI Training Coach', 'content' => 'AI Training Coach — your personal intelligence tutor inside XR Doctor — is the Next-Gen Medical Standard for self-guided XR training powered by AI precision.'],
                            ['icon' => '🌐', 'label' => 'AI Teamwork Mentor', 'content' => 'AI Teamwork Mentor brings intelligent coordination and insight to XR Doctor group sessions and turns an XR meeting into a living knowledge document.'],
                        ],
                    ],
                ],
                'cta_title' => 'Inside XR Doctor - uncover the full system',
                'cta_sub' => 'how it defines the Next-GeN Medical Standard',
                'cta_href' => '#block-1-9-10',
            ],
        ],
        [
            'type' => 'hologram_stories',
            'id' => 'block-1-9-10',
            'props' => [
                'badge'      => 'Star Stories',
                'heading'    => 'Embrace Epic Hologram Stories',
                'subheading' => 'Saving Patients and Empowering Doctors',
                'stories' => [
                    [
                        'icon'    => "\xF0\x9F\xA6\xB4",
                        'tags'    => ['Traumatology Orthopaedics', 'Endoprosthesis'],
                        'summary' => 'Patient back to vital functionality after 10 years of deep suffering. Surgeons got revolutionary tool for interactive visualisation & measurement on operation area.',
                        'title'   => 'Patient restored full function after a decade of suffering',
                        'body'    => [
                            'After 10 years of deep suffering, a patient was able to return to vital functionality thanks to holographic technology. Surgeons received a revolutionary tool for uncovering interactive visualisation and measurement on the operation area.',
                            'The hologram enabled precise spatial understanding of the anatomy, allowing the surgical team to plan and execute the procedure with unprecedented accuracy.',
                        ],
                        'footer'  => 'Interactive holographic measurement changed the outcome of the surgery',
                    ],
                    [
                        'icon'    => "\xF0\x9F\x92\x80",
                        'tags'    => ['Maxillofacial Otolaryngology', 'Plastic Surgery'],
                        'summary' => 'Patient Avoided rib removal. Surgeons got new diagnostic and planning tools. Surgery plan completely turned out — surgery completed a few hours faster.',
                        'title'   => 'A 17-year-old girl from an orphanage with severe congenital maxillofacial and ENT pathology',
                        'body'    => [
                            'Young lady had difficulty breathing and her appearance caused constant ridicule from her peers. Long-term preparation was carried out for a complex interdisciplinary operation by a team of 3 PhDs — maxillofacial, ENT, plastic surgeon. The original plan was to remove the rib and form a nasal septum from it. After using the hologram for diagnosis, they found out that the girl had enough cartilage tissue, which all traditional medical studies did not show.',
                            'Almost immediately after using the hologram, it was decided to change the operation plan — not to remove the rib and thereby not cause significant damage to the health of the young girl. Holograms were also used for precise planning — doctors were able to form a nasal septum from their own cartilage for free breathing and restore the beauty of the face. A few months later the girl was adopted.',
                        ],
                        'footer'  => 'First in the world maxillofacial surgery with using holograms on an open skull!',
                    ],
                    [
                        'icon'    => "\xF0\x9F\xA6\xB7",
                        'tags'    => ['Dentistry', 'Mental Health'],
                        'summary' => 'Patient Education with holograms helped reduce patient anxiety. Dentist fascinated by hologram tool for daily practice and joined the XR Doctor collaboration team.',
                        'title'   => '32-year old product manager from XR Doctor team with an unexpectedly dental problem',
                        'body'    => [
                            'Our healthy and active employee discovered that she had problems while eating. She was diagnosed with a complex problem with her jaw and had to undergo long-term treatment in several stages. She was very afraid of the upcoming procedures and tried to understand what and how she would do.',
                            'Taking care of the physical and mental health of our employees, we decided to help her and made a set of holograms based on 3D models from the doctor. The doctor, using holograms, explained to her the entire course of treatment, after which she went to the procedures completely calm and worked successfully, without being distracted by mental problems.',
                        ],
                        'footer'  => 'Dentist Fascinated By Using Holograms During Treatment And Joined XR DOCTOR Team',
                    ],
                    [
                        'icon'    => "\xF0\x9F\x8E\xAF",
                        'tags'    => ['Oncology', 'Traumatology'],
                        'summary' => 'Surgery planning with holograms in complicated oncology cases helps experienced surgeons. Surgery time reduced 6 times. No vital vessels or nerves damaged.',
                        'title'   => 'A 20-year-old successful student with a malignant tumor on the bone of the forearm',
                        'body'    => [
                            'This girl\'s attending physician accidentally heard from a traumatologist from a neighbouring hospital department about an ongoing innovative project with holograms. He was very worried about the difficult access to the tumour and asked to make a hologram for placement on the body during surgery for this patient.',
                            'An experienced doctor, for the first time in his life, put on AR glasses to plan a complex operation. Within a few minutes, under the guidance of a doctor from an innovative project, he learned to work with glasses — in minutes he saw how to bypass the areas most dangerous for damage during surgery. A few days later, instead of the 3 hours initially planned, he gained access to the tumour in 30 minutes, using glasses and holograms on his own.',
                        ],
                        'footer'  => 'Surgeons gained access to the tumour in 30 minutes instead of the 3 hours initially planned',
                    ],
                    [
                        'icon'    => "\xE2\x9D\xA4\xEF\xB8\x8F",
                        'tags'    => ['Pediatric', 'Cardiac Surgery'],
                        'summary' => 'Enlarged interactive holograms helped discover a new surgery method for pediatric cardiac pathology without stopping the heart during high-risk surgery.',
                        'title'   => 'A 5-year-old boy with a complex congenital heart defect',
                        'body'    => [
                            'For every 1000 births — 15 children have a heart defect. 25% die in early infancy, 50% within the first year of life. Technology allows 80% to reach middle age. A serious treatment challenge is the small size of the heart and vital structures in the surgical field. Stopping the heart in open surgery puts the child\'s life and health at risk.',
                            'After discussions with pediatric surgeons, a hologram of a 5-year-old boy\'s heart scheduled for surgery was created. During the consultation, doctors, for the first time using glasses and seeing holograms, saw how it is possible to perform surgery for this heart defect without stopping the heart. With such a high-tech tool and interactive advanced visualization, new, less harmful and more successful surgical techniques can be created, saving the lives of young patients.',
                        ],
                        'footer'  => 'Hologram is the tool for interactive advanced visualization and discovering new methods',
                    ],
                ],
            ],
        ],
        [
            'type' => 'impact_stats',
            'id' => 'block-1-10-impact',
            'props' => [
                'heading'  => "Join XR Doctor. A New Leap\nin Healthcare Industry",
                'subtitle' => 'Medical doctor need 10 years to master skills. XR Doctor bridges the gap to accelerate the expertise.',
                'stats' => [
                    [
                        'value' => '+60%',
                        'label' => 'Improve treatment outcomes',
                        'note'  => '*Built on doctors\' expertise with holograms',
                    ],
                    [
                        'value' => '+275%',
                        'label' => 'Increase skills confidence',
                        'note'  => '*Proven through large-scale studies',
                    ],
                    [
                        'value' => '+400%',
                        'label' => 'Faster education process',
                        'note'  => '*Validated through scientific research',
                    ],
                ],
            ],
        ],
        [
            'type' => 'clinical_circles',
            'id' => 'block-1-10-clinic',
            'props' => [
                'label'   => 'XR Doctor Platform. Made by XR Doctor team \xe2\x80\x93 from real operations to daily work',
                'heading' => 'Born in Real Clinical Practice',
                'subhead' => 'Build together with Real Doctors & Patients',
                'tagline' => "From OR to Classroom \xe2\x80\x93 we\xe2\x80\x99ve been there. Every feature answers a Real Need.",
                'circles' => [
                    [
                        'src' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&h=400&fit=crop',
                        'alt' => 'Surgery with AR glasses',
                    ],
                    [
                        'src' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&h=400&fit=crop',
                        'alt' => 'Doctor using hologram with patient',
                    ],
                    [
                        'src' => 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?w=400&h=400&fit=crop',
                        'alt' => 'Medical team with holograms',
                    ],
                ],
            ],
        ],
        [
            'type' => 'expertise_banner',
            'id' => 'block-1-11',
            'props' => [
                'title'    => 'Bridging Expertise & Innovation',
                'subtitle' => 'XR Doctor unites cutting-edge technology with top medical expertise to deliver change and create pioneering XR Platform with built-in AI reshaping the medical profession.',
            ],
        ],
        [
            'type' => 'team_visioners',
            'id' => 'block-1-11-team',
            'props' => [
                'card_title'    => 'Visioners',
                'card_subtitle' => 'behind XR Doctor',
                'achievements' => [
                    [
                        'title' => 'Pioneered',
                        'items' => [
                            'AR tech, 2.8B+ users',
                            'Holograms in real surgery',
                            'Product with built-in AI in 2008',
                        ],
                    ],
                    [
                        'title' => 'Shaped',
                        'items' => [
                            'top geo app, 140M+ users',
                            'top global messenger, 1.3B+ users',
                        ],
                    ],
                    [
                        'title' => 'Brought to Market',
                        'items' => [
                            "world\xe2\x80\x99s 1st haptic-feedback VR suit",
                            'ground-breaking haptic gloves',
                        ],
                    ],
                    [
                        'title' => 'Awarded',
                        'items' => [
                            'Red Dot, Future Unicorn',
                            'CES Innovation, World Summit',
                        ],
                    ],
                    [
                        'title' => 'Collaborated',
                        'items' => [
                            'NASA, Boeing, Verizon, Vodafone',
                            'Formula 1, Accenture, Sanofi',
                        ],
                    ],
                ],
                'photos' => [
                    ['src' => $img('i', 580, 430), 'alt' => 'XR Doctor in clinical setting'],
                    ['src' => $img('j', 580, 430), 'alt' => 'XR Doctor surgical team'],
                ],
                'heading' => "Next-GeN Minds\nCode the Leap",
                'body'    => 'We are pioneers of XR/AI and global innovators. Our multidisciplinary team brings together deep scientific and practice expertise in medicine and cutting-edge technologies. Doctors of Science, skilled PhD researchers in Medicine & Computer Science, top-level developers and bioengineers, talented 3D artists have been building next-gen solution for daily medical life.',
                'stats' => [
                    ['value' => '5+',  'label' => 'years of R&D'],
                    ['value' => '20+', 'label' => 'engaged countries'],
                ],
            ],
        ],
        [
            'type' => 'floating_plank',
            'id' => 'block-1-12',
            'props' => [
                'title' => 'Built for real hospitals and universities',
                'bullets' => ['HIPAA-aware workflows', 'SSO & enterprise roles', 'Offline-capable headsets'],
                'image' => $img('h', 600, 800),
            ],
        ],
        [
            'type' => 'timeline_gradient',
            'id' => 'block-1-15',
            'props' => [
                'heading' => 'From scan to shared hologram',
                'steps' => [
                    ['title' => 'Ingest', 'text' => 'Upload DICOM or mesh; automatic prep.'],
                    ['title' => 'Map', 'text' => 'Align to room and patient context.'],
                    ['title' => 'Collaborate', 'text' => 'Invite peers — same view, live.'],
                ],
            ],
        ],
        [
            'type' => 'gallery_three',
            'id' => 'block-1-16-17',
            'props' => [
                'heading' => 'Headsets we optimize for',
                'slides' => [
                    ['image' => $img('i', 500, 400), 'title' => 'Device family A'],
                    ['image' => $img('j', 500, 400), 'title' => 'Device family B'],
                    ['image' => $img('k', 500, 400), 'title' => 'Device family C'],
                ],
                'interval_ms' => 4000,
            ],
        ],
        [
            'type' => 'starfield_cta',
            'id' => 'block-1-19',
            'props' => [
                'infinity_symbol' => '∞',
                'title' => 'Ready to see your first holographic case?',
                'button_label' => 'Request Demo',
                'href' => '#hubspot-demo',
            ],
        ],
        [
            'type' => 'text_reveal_simple',
            'id' => 'block-1-20',
            'props' => [
                'lines' => [
                    'Precision. Presence. Scale.',
                    'XR Doctor brings teams into the same spatial truth.',
                ],
            ],
        ],
        [
            'type' => 'closing_block',
            'id' => 'block-closing',
            'props' => [
                'line1' => 'Scale Your Success with XR Doctor',
                'line2' => 'Next Level Starts Now',
            ],
        ],
    ];
}

function xr_default_professionals_blocks(): array
{
    $img = static function (string $suffix, int $w, int $h): string {
        return xr_figma_asset('professionals', 'pro-' . $suffix, 'xrprof' . $suffix, $w, $h);
    };

    return [
        [
            'type' => 'hero_twinkle',
            'id' => 'p-2-intro',
            'props' => [
                'image' => $img('1', 1440, 900),
                'title' => 'XR Doctor for Professionals',
                'subtitle' => 'Tools that match the pace of your practice.',
            ],
        ],
        [
            'type' => 'blue_video_freeze',
            'id' => 'p-2-2',
            'props' => [
                'mp4' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'poster' => $img('2', 1440, 800),
                'text' => 'Single-play cinematic intro — holds the last frame, then scroll to continue.',
            ],
        ],
        [
            'type' => 'two_column_features',
            'id' => 'p-2-3',
            'props' => [
                'left_title' => 'Daily workflow',
                'left_text' => 'Structured like block 1.8 — without tab chrome: clear columns and media.',
                'right_image' => $img('3', 700, 500),
            ],
        ],
        [
            'type' => 'carousel_four_cards',
            'id' => 'p-2-4',
            'props' => [
                'cards' => [
                    ['title' => 'Diagnostics', 'text' => 'Layered holograms for rounds and planning.', 'image' => $img('4a', 400, 240)],
                    ['title' => 'Education', 'text' => 'Repeatable scenarios with measurable outcomes.', 'image' => $img('4b', 400, 240)],
                    ['title' => 'Research', 'text' => 'Shareable models for multi-site studies.', 'image' => $img('4c', 400, 240)],
                    ['title' => 'Operations', 'text' => 'Deploy kits with enterprise controls.', 'image' => $img('4d', 400, 240)],
                ],
            ],
        ],
        [
            'type' => 'youtube_heading',
            'id' => 'p-2-5',
            'props' => [
                'heading' => 'Walkthrough for clinical teams',
                'youtube_id' => 'dQw4w9WgXcQ',
            ],
        ],
        [
            'type' => 'animated_heading_tabs',
            'id' => 'p-2-6',
            'props' => [
                'prefix' => 'XR Doctor for Every You.',
                'rotating' => ['Your Vision.', 'Your Tool.', 'XR Doctor.'],
                'interval_ms' => 2800,
                'panels' => [
                    ['label' => 'Clinicians', 'text' => 'Same animated headline behavior on each horizontal tab.'],
                    ['label' => 'Educators', 'text' => 'Bold segment cycles automatically for attention.'],
                    ['label' => 'Leaders', 'text' => 'Swap copy per audience while motion stays consistent.'],
                ],
            ],
        ],
        [
            'type' => 'tabs_three_horizontal',
            'id' => 'p-2-7',
            'props' => [
                'tabs' => [
                    ['label' => 'Overview', 'body' => 'Three horizontal tabs — reviews-style rhythm.'],
                    ['label' => 'Evidence', 'body' => 'Link out to studies or PDFs from your CMS.'],
                    ['label' => 'Security', 'body' => 'Describe SSO, audit, and data residency.'],
                ],
            ],
        ],
        [
            'type' => 'layered_star',
            'id' => 'p-2-8',
            'props' => [
                'title' => 'Immersive layer stack',
                'subtitle' => 'As block 1.3 — tuned for Professionals narrative.',
                'base_color' => '#0d1b2a',
            ],
        ],
        [
            'type' => 'gallery_three',
            'id' => 'p-2-9',
            'props' => [
                'heading' => 'Hardware fit (auto carousel)',
                'slides' => [
                    ['image' => $img('9a', 500, 400), 'title' => 'Fit A'],
                    ['image' => $img('9b', 500, 400), 'title' => 'Fit B'],
                    ['image' => $img('9c', 500, 400), 'title' => 'Fit C'],
                ],
                'interval_ms' => 3500,
            ],
        ],
        [
            'type' => 'text_heading_anim',
            'id' => 'p-2-10',
            'props' => [
                'headline' => 'Designed for accountable outcomes',
                'paragraph' => 'Animated headline entrance + supporting copy for scanning.',
            ],
        ],
        [
            'type' => 'tabs_top_images',
            'id' => 'p-2-11',
            'props' => [
                'title' => 'Your Workflow with XR Doctor',
                'subtitle' => 'Easy Start. Simple Steps. Next-GeN Reality.',
                'footer' => 'Built for real professionals. Ready to be a part of your daily work.',
                'steps' => [
                    ['title' => 'I. Login', 'text' => 'Sign up in seconds and step into your XR workspace', 'image' => ''],
                    ['title' => 'II. Upload', 'text' => 'Advancing holographic interactive visualization tools for medical excellence', 'image' => ''],
                    ['title' => 'III. Work', 'text' => 'Apply pioneering professional hologram tools in daily work', 'image' => ''],
                    ['title' => 'IV. Share', 'text' => 'Collaborate seamlessly with shared holograms onsite and remotely', 'image' => ''],
                    ['title' => 'V. Scale', 'text' => 'Scale worldwide holographic work across roles and tasks', 'image' => ''],
                    ['title' => 'VI. Stream', 'text' => 'Show view from glasses globally to any screens and YouTube', 'image' => ''],
                ],
            ],
        ],
        [
            'type' => 'pricing_creative',
            'id' => 'p-2-13',
            'props' => [
                'eyebrow' => 'XR Doctor knows what matters & designed plans that work for you',
                'title' => "Choose Your Plan\n& Let XR Doctor\nEmpower Your Every Day",
            ],
        ],
        [
            'type' => 'coming_soon_anim',
            'id' => 'p-2-14',
            'props' => [
                'title' => "Affordable\nPrices for\nEveryone",
                'tabs' => [
                    ['label' => 'Glasses Licence'],
                    ['label' => 'Content Cost'],
                ],
                'panels' => [
                    [
                        'cards' => [
                            [
                                'badge' => 'VR Glasses',
                                'price' => '$65 mo/$699 year',
                                'items' => [
                                    'Inside a fully virtual space',
                                    'Work with interactive hologram',
                                    'Explore clinical and simulated cases',
                                    'Precise diagnosis and preparation',
                                    'Immersive planning, executing, training',
                                ],
                                'cta' => 'Pre-Order',
                            ],
                            [
                                'badge' => 'AR Glasses',
                                'price' => '$95 mo/$999 year',
                                'items' => [
                                    'Matched with the physical world',
                                    'Work with interactive mapped hologram',
                                    'Handle standard and complex cases',
                                    'Enhance diagnostics via real overlay',
                                    'Shared mapped hologram space',
                                ],
                                'cta' => 'Pre-Order',
                            ],
                        ],
                    ],
                    [
                        'cards' => [
                            [
                                'badge' => 'FREE',
                                'items' => [
                                    'Use your own 3D models',
                                    'Upload as many as you need',
                                    'Built to fit the platform',
                                    'Included in every plan',
                                    'No extra cost - start now',
                                ],
                            ],
                            [
                                'badge' => 'From $7',
                                'items' => [
                                    'Standard CT-scans - $10',
                                    'Prepaid bundles: 10 CT-scans - $90',
                                    '25 CT-scans - $200',
                                    '50 CT-scans - $350',
                                    'Usage tracked in your account',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        [
            'type' => 'stagger_lines',
            'id' => 'p-2-15',
            'props' => [
                'columns' => [
                    [
                        'title' => 'World-Class Product',
                        'text' => 'XR Doctor - Next-GeN interactive holographic tool with AI support to leap your growth & boost success setting the new world standard',
                    ],
                    [
                        'title' => 'World-Wide Network',
                        'text' => 'Build a global network & become part of XR Doctor community for sharing expertise, hologram-guided telementoring via instant access to XR/AI-powered shared space',
                    ],
                    [
                        'title' => 'World-Level Mastery',
                        'text' => 'Achieve professional excellence & bring your skills to global standards with live layered mapped holograms & enhanced AI Clinical Assistance',
                    ],
                ],
            ],
        ],
        [
            'type' => 'image_pulse_cta',
            'id' => 'p-2-16',
            'props' => [
                'badge' => 'Stay tuned.',
                'title' => 'XR Doctor. Already Here.',
            ],
        ],
        [
            'type' => 'reveal_outro',
            'id' => 'p-2-17',
            'props' => [
                'image' => '',
                'text' => "Take the World into Your Hands.\nUnlock XR Doctor Power for Your Mastery & Edge.",
            ],
        ],
        [
            'type' => 'preorder_banner',
            'id' => 'p-2-18',
            'props' => [
                'title' => "Leap into Your Next-GeN\nMedical Excellence",
                'subtitle' => 'Pre-Order Now - Reserve Your Access',
                'note' => 'Get 3 Years Bonus - 1 Free Month Each Year',
                'button_label' => 'Pre-Order Now',
                'href' => '#hubspot-demo',
            ],
        ],
    ];
}

/**
 * Страница 3 — Institutions (ТЗ из !!! 3.1 Комментарии Institutions.docx).
 */
function xr_default_institutions_blocks(): array
{
    $img = static function (string $suffix, int $w, int $h): string {
        return xr_figma_asset('institutions', 'inst-' . $suffix, 'xrinst' . $suffix, $w, $h);
    };
    $v = 'https://www.w3schools.com/html/mov_bbb.mp4';

    return [
        [
            'type' => 'carousel_two',
            'id' => 'i-3-1',
            'props' => [
                'heading' => 'XR Doctor -Your Next-GeN Standard is Here',
                'left_title' => "Reinvent\nDaily Work\n& Education",
                'left_text' => "Define Next-GeN Standard with\nXR Doctor ALL-IN-ONE Platform for\nClinics Practice\nMedical Education\nScientific Research",
                'right_footer' => 'Behind Next-GeN Edge',
                'slides' => [
                    ['image' => $img('31a', 1200, 680), 'caption' => 'Campus-wide deployment'],
                    ['image' => $img('31b', 1200, 680), 'caption' => 'Clinical integration'],
                ],
                'interval_ms' => 4500,
            ],
        ],
        [
            'type' => 'blue_video_freeze',
            'id' => 'i-3-2',
            'props' => [
                'mp4' => $v,
                'poster' => $img('32', 1440, 780),
                'text' => 'Block 3.2 — как 2.2: один проигрыш видео, затем последний кадр; на странице единая анимация появления текста.',
                'title' => 'ALL-IN-ONE',
                'line2' => 'XR Doctor Bring Together',
                'line3' => 'XR Doctor Assistant & Training',
            ],
        ],
        [
            'type' => 'white_text_section',
            'id' => 'i-3-3',
            'props' => [
                'title' => "Everything Changes\nwith XR Doctor",
                'subtitle' => 'AI-powered XR Platform - tailored for your medical reality from hospitals to universities',
                'body' => 'White content blocks (3.3, 3.9, 3.12, 3.14) — чистая типографика для политик, контрактов и программ обучения.',
            ],
        ],
        [
            'type' => 'layered_star',
            'id' => 'i-3-4',
            'props' => [
                'title' => 'Next-GeN Care for Every Task',
                'chip_a' => 'XR Doctor',
                'chip_b' => 'Exclusive',
                'interval_ms' => 4200,
                'slides' => [
                    ['subtitle' => 'Leap Patient Care with Spatial Precision', 'image' => ''],
                    ['subtitle' => 'Boost Mastery with True-to-Life Simulations', 'image' => ''],
                    ['subtitle' => 'Harness Next-Gen Medical Infrastructure', 'image' => ''],
                ],
            ],
        ],
        [
            'type' => 'tabs_youtube_loop',
            'id' => 'i-3-5',
            'props' => [
                'eyebrow' => 'Meet the Next-GeN Tele-Prepresence Standard for daily work and education.',
                'title' => 'XR Doctor Tele-Mentoring',
                'subtitle' => 'Interactive Shared Holographic Space with AI support',
                'heading' => 'Institutional walkthroughs',
                'tabs' => [
                    [
                        'label' => 'Overview',
                        'mode' => 'youtube_click',
                        'poster' => $img('35a', 800, 480),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'play_label' => 'Play',
                    ],
                    [
                        'label' => 'Campus loop',
                        'mode' => 'video_loop',
                        'poster' => $img('35b', 800, 480),
                        'mp4' => $v,
                    ],
                    [
                        'label' => 'Ops loop',
                        'mode' => 'video_loop',
                        'poster' => $img('35c', 800, 480),
                        'mp4' => $v,
                    ],
                ],
            ],
        ],
        [
            'type' => 'before_after',
            'id' => 'i-3-6',
            'props' => [
                'overlay_title' => 'Collaborate Globally for All',
                'overlay_text' => 'Both the same with Real – or – Digital Patient. Move & See',
                'before' => ['image' => $img('36a', 900, 560), 'label' => 'Before'],
                'after' => ['image' => $img('36b', 900, 560), 'label' => 'After'],
            ],
        ],
        [
            'type' => 'saas_split',
            'id' => 'i-3-7',
            'props' => [
                'line_1' => 'Discover How XR Doctor',
                'line_2' => 'Redefines Care Delivery',
                'line_3' => 'for Next-LeveL Excellence',
            ],
        ],
        [
            'type' => 'orbit_cards',
            'id' => 'i-3-8',
            'props' => [
                'title' => '',
                'intro_title' => 'XR Doctor Assistant',
                'intro_subtitle' => "Bring Holographic Precision\nto Every Clinical Step",
                'intro_body' => 'From diagnostic to documentation – one tool ready to use anywhere in your workflow.',
                'center_image' => $img('310a', 720, 720),
                'center_label' => 'XR DOCTOR',
                'pill_label_icon' => $img('310b', 160, 160),
                'features' => [
                    ['title' => 'Instant Deployment', 'text' => 'Start using within hours, no change to infrastructure'],
                    ['title' => 'Seamless Integration', 'text' => 'Work with your existing system and imaging data'],
                ],
                'cards' => [
                    ['label' => "Team\nWork", 'sub' => 'SSO'],
                    ['label' => 'Planning', 'sub' => 'LMS'],
                    ['label' => 'Diagnostic', 'sub' => 'Audit'],
                    ['label' => 'Execution', 'sub' => 'SLA'],
                    ['label' => "Tele-\nMentoring", 'sub' => 'Outcome Analysis'],
                    ['label' => "Case\nRecords", 'sub' => 'Patient Education'],
                    ['label' => "Outcome\nAnalysis", 'sub' => 'Team Work'],
                    ['label' => "Patient\nEducation", 'sub' => 'Planning'],
                ],
            ],
        ],
        [
            'type' => 'white_text_section',
            'id' => 'i-3-9',
            'props' => [
                'title' => 'Procurement-friendly packages',
                'body' => 'Повторяющийся белый блок 3.9 — описание лицензий, сроков пилота и расширения.',
                'line_1' => 'ALL-IN-ONE',
                'line_2' => 'XR Doctor Assistant',
                'line_3' => 'Full-Cycle Clinical Product',
            ],
        ],
        [
            'type' => 'white_planks',
            'id' => 'i-3-10',
            'props' => [
                'title' => '',
                'columns' => [
                    [
                        [
                            'variant' => 'dark',
                            'image' => $img('310a', 640, 360),
                            'title_a' => 'Diag',
                            'title_b' => 'nostic',
                            'body' => 'Invest in XR Doctor — a scalable, world-changing solution with high returns. Enter a fast-growing market with massive revenue potential and a clear path to global impact.',
                        ],
                        [
                            'variant' => 'white',
                            'title_a' => 'Team',
                            'title_b' => 'work',
                            'body' => 'Unified shared holographic space offers unmatched opportunities for teamwork. Connecting any team size regardless of distance as if working side by side, it lets clinicians interact with the same hologram both for work and education – from local consultation to worldwide workshops and streaming. Expertise becomes an instantly shared and scalable asset across institutions and borders, establishing a cost-effective global standard.',
                        ],
                        [
                            'variant' => 'summary',
                            'lines' => [
                                'World - Class Solution',
                                'World - Scale Product',
                                'World - Wide Connect',
                            ],
                        ],
                    ],
                    [
                        [
                            'variant' => 'white',
                            'title_a' => 'Treatment',
                            'title_b' => ' Planning',
                            'body' => 'Mapped holograms in real volume and size overlays on patient or manikin let clinicians compare incision, access routes or broader treatment pathways, help choose the optimal approach faster, rehearse complex cases individually or with the team across locations, and dramatically shorten planning time while reducing intraoperative risks.',
                        ],
                        [
                            'variant' => 'dark',
                            'image' => $img('310b', 640, 360),
                            'title_a' => 'Tele-',
                            'title_b' => 'Mentoring',
                            'body' => 'Our solutions are forward-thinking and rely on seamless connectivity across the world. Work with us to bring XR into real-world clinical use.',
                        ],
                        [
                            'variant' => 'white',
                            'title_a' => 'Case',
                            'title_b' => ' Record',
                            'body' => 'Real procedures become unique living case records with next-gen hologram visualisation — enrich patient history and become part of a clinical holographic library turning high-value, often inaccessible medical experience into inner clinic and global shared knowledge, strengthening clinical evidence, supporting insurance trust and opening new levels of education and training.',
                        ],
                    ],
                    [
                        [
                            'variant' => 'white',
                            'title_a' => 'Treatment',
                            'title_b' => ' Execution',
                            'body' => 'The interactive layered hologram set with mapped fixed real-size and scalable types combined with a powerful built-in measurement tool delivers next-level clinical support from high-risk surgeries to everyday interventional and non-invasive procedures, instead of heavy, slow-to-deploy equipment – light, affordable glasses, ready in seconds in any place.',
                        ],
                        [
                            'variant' => 'white',
                            'title_a' => 'Patient',
                            'title_b' => ' Education',
                            'body' => "Understanding of diagnosis and treatment with holograms makes patient education clearer, faster and more trustworthy.\n\nTrue-to-life volumetric hologram visualization reduces patient fears, speeds up agreement to treatment, saves clinical time for more patients, earning lasting trust from both patients and insurance companies as strong diagnostic evidence.",
                        ],
                        [
                            'variant' => 'white',
                            'title_a' => 'Outcome',
                            'title_b' => ' Analysis',
                            'body' => 'XR Doctor creates a new layer of clinical intelligence – turns every case into a living shared knowledge system with AI support. Automatically collected outcome data builds a comprehensive view of treatments, reveals hidden patterns and risks, shows what methods work best and where complications too often occur. This evidence improves care and strengthens trust with both patients and insurers—saving lives and money.',
                        ],
                    ],
                ],
            ],
        ],
        [
            'type' => 'orbit_cards',
            'id' => 'i-3-11',
            'props' => [
                'title' => '',
                'stack_description' => '',
                'intro_title' => 'XR Doctor Assistant Brings',
                'intro_subtitle' => 'Uncover Advantages',
                'intro_body' => '',
                'center_image' => $img('311', 640, 640),
                'center_label' => '',
                'pill_label_icon' => '/uploads/2026/04/3f348185804549cc5b8f5299.png',
                'features' => [],
                'center_pills' => ['Clinical Assistant', 'Training Coach', 'Teamwork Mentor'],
                'footer_line_a' => 'One Platform.',
                'footer_line_b' => 'Infinite Possibilities.',
                'cards' => [
                    ['label' => 'Unmatched Industry Edge', 'sub' => ''],
                    ['label' => 'Up - Level Professionals', 'sub' => ''],
                    ['label' => 'More Satisfied Patients', 'sub' => ''],
                    ['label' => 'Smart and Efficient Learning', 'sub' => ''],
                    ['label' => 'Time and Money Saving', 'sub' => ''],
                    ['label' => 'Ground - Breaking Research', 'sub' => ''],
                ],
            ],
        ],
        [
            'type' => 'white_text_section',
            'id' => 'i-3-12',
            'props' => [
                'title' => 'Security & compliance posture',
                'headline' => 'Residency, audit trails & access control',
                'body' => 'Блок 3.12 — кратко о данных, регионах хранения и аудите доступа к сценариям.',
            ],
        ],
        [
            'type' => 'pricing_swapped',
            'id' => 'i-3-13',
            'props' => [
                'tagline' => 'Easy. Fast. Effective.',
                'headlines' => [
                    'Choose Your Plan & Leap',
                    'with Next-GeN',
                    'XR Doctor Assistant',
                ],
                'subtitle' => 'Equip your team to Work Smarter and Scale your Impact & Reach',
                'tabs' => [
                    ['label' => 'XR Doctor', 'subtitle' => 'Equip your team to Work Smarter and Scale your Impact & Reach'],
                    ['label' => 'Exclusive', 'subtitle' => 'Dedicated rollout, priority support, and tailored SLAs for large institutions.'],
                ],
                'tabs_aria' => 'Pricing plan category',
            ],
        ],
        [
            'type' => 'pricing_three_tiers',
            'id' => 'i-3-14',
            'props' => [
                'cta_label' => 'Reserve Access',
                'cta_note' => 'No payment required now – your place will be saved',
                'tiers' => [
                    [
                        'pill' => 'Fast Start. Real XR',
                        'name' => 'STARTER',
                        'price_month' => '$599',
                        'price_year' => '$5999',
                        'users' => '3-5 users',
                        'features' => [
                            '30 CT-based holograms for all team',
                            '3D model-based holograms as needed',
                            'Full-featured tool for AR/VR Glasses',
                            '5 users Shared Holographic Workspace',
                            'Public Session Streaming via screen',
                            'Quick Video Onboarding',
                            'Standard support',
                        ],
                        'footnote' => '*Can be purchased jointly by partners',
                        'cta_href' => '#',
                    ],
                    [
                        'pill' => 'Daily XR Power',
                        'name' => 'PRO (Recommended)',
                        'price_month' => '$999',
                        'price_year' => '$9999',
                        'users' => '6-10 users',
                        'recommended' => true,
                        'features' => [
                            '50 CT-based holograms for all team',
                            '3D model-based holograms as needed',
                            'Full-featured tool for AR/VR Glasses',
                            '10 users Shared Holographic Workspace',
                            'Public & Private Session Streaming via screen',
                            'Quick Video Onboarding',
                            'Priority support',
                        ],
                        'footnote' => '*Additional CT-based holograms add-on',
                        'cta_href' => '#',
                    ],
                    [
                        'pill' => 'XR for Large Teams',
                        'name' => 'ENTERPRISE',
                        'price_month' => '$2499',
                        'price_year' => '$24999',
                        'users' => '11-20 users',
                        'features' => [
                            '120 CT-based holograms for all team',
                            '3D model-based holograms as needed',
                            'Full-featured tool for AR/VR Glasses',
                            '25 users Shared Holographic Workspace',
                            'Public & Private Session Streaming via screen',
                            'Quick Video Onboarding',
                            'Extended Priority support',
                        ],
                        'footnote' => '*For 20+ users - volume-based package',
                        'cta_href' => '#',
                    ],
                ],
            ],
        ],
        [
            'type' => 'tabs_dual_carousel',
            'id' => 'i-3-15',
            'props' => [
                'heading' => 'Tabs with rotating pairs (как 1.8, горизонтальная смена)',
                'tabs' => [
                    [
                        'label' => 'Clinical',
                        'slides' => [
                            ['image' => $img('315a', 700, 420), 'text' => 'Rounds hologram — читаемый интервал.', 'interval_ms' => 5000],
                            ['image' => $img('315b', 700, 420), 'text' => 'Surgical planning view — вторая вставка.', 'interval_ms' => 5000],
                        ],
                    ],
                    [
                        'label' => 'Education',
                        'slides' => [
                            ['image' => $img('315c', 700, 420), 'text' => 'Аудитория + AR.', 'interval_ms' => 4500],
                            ['mp4' => $v, 'text' => 'Короткий клип — длительность по видео.', 'interval_ms' => 8000],
                        ],
                    ],
                    [
                        'label' => 'Research',
                        'slides' => [
                            ['image' => $img('315d', 700, 420), 'text' => 'Мультисайтовый кейс.', 'interval_ms' => 5000],
                            ['image' => $img('315e', 700, 420), 'text' => 'Публикации и данные.', 'interval_ms' => 5000],
                        ],
                    ],
                ],
            ],
        ],
        [
            'type' => 'product_tabs',
            'id' => 'i-3-16',
            'props' => [
                'tabs' => ['Вкладка 1', 'Вкладка 2', 'Вкладка 3'],
                'active_tab' => 0,
                'panels' => [
                    [
                        'title' => 'Institutional workspace',
                        'lead' => 'Блок 3.16 — по аналогии с 1.8: три вкладки, разный контент.',
                        'body' => 'Единые политики доступа, роли преподавателей и клиницистов.',
                        'emphasis' => 'Один контракт — несколько площадок.',
                        'sidebar' => 'Масштабирование сессий и отчётность для руководства.',
                        'features' => [['label' => 'Multi-site'], ['label' => 'RBAC'], ['label' => 'Analytics']],
                        'bottom_title' => 'Deeper dive',
                        'bottom_link' => ['label' => 'Download one-pager', 'href' => '#'],
                    ],
                    [
                        'title' => 'Teaching at scale',
                        'lead' => 'Сценарии для групп и индивидуальных треков.',
                        'body' => 'Импорт кейсов, оценка прогресса, связка с LMS.',
                        'emphasis' => 'Повторяемость без потери качества.',
                        'sidebar' => 'Шаблоны курсов и библиотека моделей.',
                        'features' => [['label' => 'LMS'], ['label' => 'Rubrics'], ['label' => 'Exports']],
                        'bottom_title' => 'Curriculum office',
                        'bottom_link' => ['label' => 'Contact us', 'href' => '#hubspot-demo'],
                    ],
                    [
                        'title' => 'Research & innovation',
                        'lead' => 'Совместные исследования и песочницы.',
                        'body' => 'Обезличенные наборы, версионирование моделей, шэринг с партнёрами.',
                        'emphasis' => 'От пилота к публикации.',
                        'sidebar' => 'Соглашения о данных и этике.',
                        'features' => [['label' => 'Data use'], ['label' => 'Collaboration'], ['label' => 'Archiving']],
                        'bottom_title' => 'Office of research',
                        'bottom_link' => ['label' => 'Request workflow', 'href' => '#'],
                    ],
                ],
            ],
        ],
        [
            'type' => 'tabs_two_plain',
            'id' => 'i-3-17',
            'props' => [
                'tabs' => [
                    ['label' => 'Implementation', 'body' => 'Две вкладки как 2.7, без градиентного «хвоста» внизу блока.'],
                    ['label' => 'Adoption', 'body' => 'Фокус на смене процессов и KPI внедрения.'],
                ],
            ],
        ],
        [
            'type' => 'progress_bars_block',
            'id' => 'i-3-18',
            'props' => [
                'title' => 'Readiness indicators',
                'bars' => [
                    ['label' => 'IT readiness', 'value' => 88],
                    ['label' => 'Faculty trained', 'value' => 72],
                    ['label' => 'Headset fleet', 'value' => 64],
                    ['label' => 'Content library', 'value' => 91],
                ],
            ],
        ],
        [
            'type' => 'timeline_gradient',
            'id' => 'i-3-19',
            'props' => [
                'heading' => 'Pilot → scale (как 1.15)',
                'steps' => [
                    ['title' => 'Discover', 'text' => 'Use cases, headsets, data paths.'],
                    ['title' => 'Pilot', 'text' => 'Single department, measured outcomes.'],
                    ['title' => 'Expand', 'text' => 'Additional sites and curricula.'],
                ],
            ],
        ],
        [
            'type' => 'tabs_youtube_loop',
            'id' => 'i-3-20',
            'props' => [
                'heading' => 'Как 1.4 — ещё один таб-блок для Institutions',
                'tabs' => [
                    [
                        'label' => 'Leadership',
                        'mode' => 'youtube_click',
                        'poster' => $img('320a', 800, 480),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'play_label' => 'Watch',
                    ],
                    ['label' => 'Loop A', 'mode' => 'video_loop', 'poster' => $img('320b', 800, 480), 'mp4' => $v],
                    ['label' => 'Loop B', 'mode' => 'video_loop', 'poster' => $img('320c', 800, 480), 'mp4' => $v],
                ],
            ],
        ],
        [
            'type' => 'gallery_three',
            'id' => 'i-3-21-24',
            'props' => [
                'heading' => 'Spaces we design for (3.21 / 3.24)',
                'slides' => [
                    ['image' => $img('321a', 520, 400), 'title' => 'Simulation center'],
                    ['image' => $img('321b', 520, 400), 'title' => 'OR briefing'],
                    ['image' => $img('321c', 520, 400), 'title' => 'Lecture hall'],
                ],
                'interval_ms' => 3800,
            ],
        ],
        [
            'type' => 'highlight_box_block',
            'id' => 'i-3-22',
            'props' => [
                'title' => 'Highlight box',
                'body' => 'Блок 3.22 — как highlight-box / сочетание акцента с элементами 1.3: градиентная рамка и плотный контент.',
            ],
        ],
        [
            'type' => 'starfield_cta',
            'id' => 'i-3-25',
            'props' => [
                'infinity_symbol' => '∞',
                'title' => 'Launch an institutional pilot',
                'button_label' => 'Request Demo',
                'href' => '#hubspot-demo',
            ],
        ],
        [
            'type' => 'text_reveal_simple',
            'id' => 'i-3-26',
            'props' => [
                'lines' => [
                    'One platform. Many sites.',
                    'XR Doctor aligns clinical, academic, and research missions.',
                ],
            ],
        ],
        [
            'type' => 'reveal_outro',
            'id' => 'i-3-27',
            'props' => [
                'title' => 'Let’s design your rollout',
                'body' => 'Блок 3.27 — как 2.17: финальный призыв с мягким появлением текста.',
            ],
        ],
    ];
}

/**
 * Страница 4 — Blog (ТЗ из !!! 4.1 Комментарии Blog.docx).
 */
function xr_default_blog_blocks(): array
{
    $img = static function (string $suffix, int $w, int $h): string {
        return xr_figma_asset('blog', 'blog-' . $suffix, 'xrblog' . $suffix, $w, $h);
    };
    $v = 'https://www.w3schools.com/html/mov_bbb.mp4';

    return [
        [
            'type' => 'blog_hero',
            'id' => 'block-4-1',
            'props' => [
                'image' => $img('41', 1440, 720),
                'title' => 'XR Doctor Blog',
                'subtitle' => 'Clinical notes, releases, and research — with headline reveal on load.',
            ],
        ],
        [
            'type' => 'intro_gradient',
            'id' => 'block-4-2-intro',
            'props' => [
                'headline_line1' => 'From the field',
                'headline_line2' => 'and the lab',
                'body' => 'Masonry-style grid (4.2): one pinned post links to the full article in block 4.3. Cross-link from Home block 1.9 points here.',
            ],
        ],
        [
            'type' => 'blog_masonry',
            'id' => 'block-4-2-grid',
            'props' => [
                'heading' => 'Latest & pinned',
                'pinned' => [
                    'badge' => 'Pinned',
                    'title' => 'How one health system scaled holographic rounds',
                    'excerpt' => 'Teaser for the pinned folder in the source set — full copy lives in block 4.3 below.',
                    'image' => $img('42pin', 800, 520),
                    'anchor' => '#block-4-3',
                    'cta' => 'Read full post →',
                ],
                'posts' => [
                    ['title' => 'Release notes — spatial anchors', 'excerpt' => 'Faster room calibration in 2.x.', 'image' => $img('42a', 400, 260)],
                    ['title' => 'Paper digest: AR telementoring', 'excerpt' => 'What the meta-analysis says.', 'image' => $img('42b', 400, 280)],
                    ['title' => 'Campus pilot checklist', 'excerpt' => 'IT + faculty readiness.', 'image' => $img('42c', 400, 300)],
                    ['title' => 'Security bulletin Q2', 'excerpt' => 'Logging and SSO updates.', 'image' => $img('42d', 400, 240)],
                    ['title' => 'Workshop recap: Barcelona', 'excerpt' => 'Hands-on with headsets.', 'image' => $img('42e', 400, 270)],
                ],
            ],
        ],
        [
            'type' => 'blog_pinned_detail',
            'id' => 'block-4-3',
            'props' => [
                'badge' => 'Pinned post',
                'title' => 'How one health system scaled holographic rounds',
                'date' => 'March 2025',
                'image' => $img('43full', 960, 540),
                'body' => "Full pinned article (4.3). Cross-links: from Home testimonials (block 1.9) via the link under the marquee, and back to that section below.\n\nThis block uses the same story as the pinned card above so anchors and navigation stay consistent.",
                'back_label' => '← Back to testimonials on Home (block 1.9)',
                'back_href' => '/#block-1-9-10',
            ],
        ],
        [
            'type' => 'tabs_youtube_loop',
            'id' => 'block-4-4',
            'props' => [
                'heading' => 'Video & loops (same pattern as block 1.4)',
                'tabs' => [
                    [
                        'label' => 'Editorial film',
                        'mode' => 'youtube_click',
                        'poster' => $img('44a', 800, 480),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'play_label' => 'Play',
                    ],
                    ['label' => 'B-roll loop A', 'mode' => 'video_loop', 'poster' => $img('44b', 800, 480), 'mp4' => $v],
                    ['label' => 'B-roll loop B', 'mode' => 'video_loop', 'poster' => $img('44c', 800, 480), 'mp4' => $v],
                ],
            ],
        ],
        [
            'type' => 'reveal_outro',
            'id' => 'block-4-5',
            'props' => [
                'title' => 'Subscribe for updates',
                'body' => 'Outro block (4.5) — same reveal pattern as Professionals block 2.17.',
            ],
        ],
    ];
}

/**
 * Страница 5 — Partner with Us (ТЗ из !!!_5_1_Комментарии_Partners_with_us.docx).
 */
function xr_default_partners_blocks(): array
{
    $img = static function (string $suffix, int $w, int $h): string {
        return xr_figma_asset('partners', 'part-' . $suffix, 'xrpart' . $suffix, $w, $h);
    };
    $v = 'https://www.w3schools.com/html/mov_bbb.mp4';

    return [
        [
            'type' => 'hero_twinkle',
            'id' => 'block-5-1',
            'props' => [
                'image' => $img('51', 1440, 900),
                'title' => 'Partner with XR Doctor',
                'subtitle' => 'Block 5.1 — fullscreen hero with twinkle overlay (same pattern as Professionals §2.1).',
            ],
        ],
        [
            'type' => 'intro_gradient',
            'id' => 'block-5-2',
            'props' => [
                'headline_line1' => 'Build the ecosystem',
                'headline_line2' => 'that scales XR care',
                'body' => 'Block 5.2 — gradient headline block (same pattern as Home §1.2).',
            ],
        ],
        [
            'type' => 'white_planks',
            'id' => 'block-5-3',
            'props' => [
                'title' => 'Partnership tracks',
                'stagger' => true,
                'planks' => [
                    ['title' => 'Technology', 'text' => 'SDKs, device certification, and co-built workflows.'],
                    ['title' => 'Distribution', 'text' => 'Regional rollout, training partners, and reseller paths.'],
                    ['title' => 'Clinical', 'text' => 'Joint studies, reference sites, and evidence generation.'],
                ],
            ],
        ],
        [
            'type' => 'partner_split_video',
            'id' => 'block-5-4',
            'props' => [
                'mp4' => $v,
                'poster' => $img('54', 900, 560),
                'title' => 'See the partner workflow',
                'body' => 'Block 5.4 — like Professionals §2.2 (video freeze) with copy and CTA beside the media.',
                'button_label' => 'Request partner deck',
                'href' => '#hubspot-demo',
                'media_position' => 'left',
            ],
        ],
        [
            'type' => 'partner_wp_icons',
            'id' => 'block-5-5',
            'props' => [
                'title' => 'What we ship together',
                'note' => 'Block 5.5 — WordPress Dashicons (official woff2) for admin-style pictograms.',
                'items' => [
                    ['dashicon' => 'f307', 'label' => 'Alliances', 'text' => 'Structured tiers for OEMs and integrators.'],
                    ['dashicon' => 'f120', 'label' => 'People', 'text' => 'Champions, clinical leads, and enablement.'],
                    ['dashicon' => 'f106', 'label' => 'Plugins', 'text' => 'Optional modules and marketplace hooks.'],
                    ['dashicon' => 'f322', 'label' => 'Programs', 'text' => 'Pilot playbooks and expansion kits.'],
                ],
            ],
        ],
        [
            'type' => 'tabs_youtube_loop',
            'id' => 'block-5-6',
            'props' => [
                'heading' => 'Partner stories on video (same pattern as Home §1.4)',
                'tabs' => [
                    [
                        'label' => 'Overview',
                        'mode' => 'youtube_click',
                        'poster' => $img('56a', 800, 480),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'play_label' => 'Play',
                    ],
                    ['label' => 'Loop A', 'mode' => 'video_loop', 'poster' => $img('56b', 800, 480), 'mp4' => $v],
                    ['label' => 'Loop B', 'mode' => 'video_loop', 'poster' => $img('56c', 800, 480), 'mp4' => $v],
                ],
            ],
        ],
        [
            'type' => 'white_planks',
            'id' => 'block-5-7',
            'props' => [
                'title' => 'Day-one partner kit',
                'stagger' => false,
                'planks' => [
                    ['title' => 'Enablement', 'text' => 'Sales tools, security FAQs, and compliance one-pagers.'],
                    ['title' => 'Support', 'text' => 'Partner success manager and joint office hours.'],
                    ['title' => 'Co-marketing', 'text' => 'Events, case studies, and press templates.'],
                ],
            ],
        ],
        [
            'type' => 'partner_split_video',
            'id' => 'block-5-8',
            'props' => [
                'mp4' => $v,
                'poster' => $img('58', 900, 560),
                'title' => 'Flip layout — same block as 5.4',
                'body' => 'Block 5.8 — media on the right; copy and button on the left.',
                'button_label' => 'Talk to partnerships',
                'href' => '#hubspot-demo',
                'media_position' => 'right',
            ],
        ],
        [
            'type' => 'video_freeze_center_image',
            'id' => 'block-5-9',
            'props' => [
                'mp4' => $v,
                'poster' => $img('59v', 1440, 800),
                'center_image' => $img('59c', 640, 400),
                'center_alt' => '',
                'caption' => 'Block 5.9 — video freeze (Home §1.6) with a centered still on top of the frame.',
            ],
        ],
        [
            'type' => 'tabs_youtube_loop',
            'id' => 'block-5-10',
            'props' => [
                'heading' => 'Larger headline tabs (same as §5.6, bigger type)',
                'heading_size' => 'lg',
                'tabs' => [
                    [
                        'label' => 'Spotlight',
                        'mode' => 'youtube_click',
                        'poster' => $img('510a', 800, 480),
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'play_label' => 'Watch',
                    ],
                    ['label' => 'Motion A', 'mode' => 'video_loop', 'poster' => $img('510b', 800, 480), 'mp4' => $v],
                    ['label' => 'Motion B', 'mode' => 'video_loop', 'poster' => $img('510c', 800, 480), 'mp4' => $v],
                ],
            ],
        ],
        [
            'type' => 'reveal_outro',
            'id' => 'block-5-11',
            'props' => [
                'title' => 'Let’s partner',
                'body' => 'Block 5.11 — outro like Professionals §2.17.',
            ],
        ],
    ];
}
