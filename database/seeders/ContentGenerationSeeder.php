<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ContentGenerationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Terapia de Pareja (Specialized Content)
        $couplesServices = [
            [
                'title' => 'Terapia de Pareja',
                'description' => 'Un espacio seguro para reconstruir la conexión, mejorar la comunicación y fortalecer el vínculo amoroso. Trabajamos juntos para superar crisis y reencontrar el equilibrio.',
                'image' => 'https://images.unsplash.com/photo-1516585427167-9f4af9627e6c?q=80&w=800&auto=format&fit=crop',
            ],
            [
                'title' => 'Consejería Prematrimonial',
                'description' => 'Prepara tu relación para el futuro. Abordamos temas clave como finanzas, familia y expectativas para construir una base sólida antes del "Sí, acepto".',
                'image' => 'https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?q=80&w=800&auto=format&fit=crop',
            ],
            [
                'title' => 'Resolución de Conflictos',
                'description' => 'Herramientas prácticas para manejar desacuerdos de manera saludable y constructiva, transformando el conflicto en una oportunidad de crecimiento.',
                'image' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?q=80&w=800&auto=format&fit=crop',
            ],
            [
                'title' => 'Reconexión Emocional',
                'description' => 'Para parejas que sienten que se han distanciado. Enfocado en recuperar la intimidad, la confianza y el afecto en la relación.',
                'image' => 'https://images.unsplash.com/photo-1621252179027-94459d27d3ee?q=80&w=800&auto=format&fit=crop',
            ],
        ];

        foreach ($couplesServices as $service) {
            Service::updateOrCreate(
                ['title' => $service['title']],
                [
                    'description' => $service['description'],
                    'price' => 1200.00,
                    'price_mxn' => 1200.00,
                    'price_usd' => 60.00,
                    'duration_minutes' => 60,
                    'type' => 'couple', // Key for filtering
                    'is_active' => true,
                    'image' => $service['image'],
                ]
            );
        }

        // 2. Gallery / Workshops Items
        $workshopItems = [
            [
                'title' => 'Conferencias',
                'description' => 'Charlas inspiradoras sobre salud mental impartidas por expertos.',
                'type' => 'conference',
                'image' => 'https://images.unsplash.com/photo-1544531696-fa37a85d38cc?q=80&w=800&auto=format&fit=crop',
            ],
            [
                'title' => 'Capacitaciones',
                'description' => 'Formación profesional y desarrollo de habilidades interpersonales.',
                'type' => 'training',
                'image' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=800&auto=format&fit=crop',
            ],
            [
                'title' => 'Pláticas',
                'description' => 'Conversatorios informales para grupos y comunidades.',
                'type' => 'talk',
                'image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=800&auto=format&fit=crop',
            ],
            [
                'title' => 'Ponencias',
                'description' => 'Presentaciones académicas y resultados de investigaciones recientes.',
                'type' => 'presentation', // Ensure this maps to frontend filter or add it
                'image' => 'https://images.unsplash.com/photo-1475721027767-p42f56b265d3?q=80&w=800&auto=format&fit=crop',
            ],
            [
                'title' => 'Talleres',
                'description' => 'Sesiones prácticas para el aprendizaje vivencial y manejo de emociones.',
                'type' => 'workshop',
                'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=800&auto=format&fit=crop',
            ],
        ];

        foreach ($workshopItems as $item) {
            Service::updateOrCreate(
                ['title' => $item['title']],
                [
                    'description' => $item['description'],
                    'price' => 0.00, // Often custom priced, set base to 0
                    'price_mxn' => 0.00,
                    'price_usd' => 0.00,
                    'duration_minutes' => 120, // Default duration
                    'type' => $item['type'],
                    'is_active' => true,
                    'image' => $item['image'],
                ]
            );
        }

        // Shop Items (Ebooks, Videos, Manuals)
        $shopItems = [
            [
                'title' => 'Guía de Ansiedad',
                'description' => 'Un eBook completo para entender y manejar la ansiedad en el día a día.',
                'type' => 'ebook',
                'image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=800&auto=format&fit=crop',
                'price_mxn' => 50,
                'price_usd' => 2.50,
            ],
            [
                'title' => 'Masterclass: Autoestima',
                'description' => 'Video curso de 2 horas sobre cómo construir una autoestima sólida.',
                'type' => 'video',
                'image' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=800&auto=format&fit=crop',
                'price_mxn' => 50,
                'price_usd' => 2.50,
            ],
            [
                'title' => 'Manual de Terapia Cognitiva',
                'description' => 'Manual técnico para estudiantes y profesionales de la salud mental.',
                'type' => 'manual',
                'image' => 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?q=80&w=800&auto=format&fit=crop',
                'price_mxn' => 50,
                'price_usd' => 2.50,
            ],
            [
                'title' => 'Meditaciones Guiadas',
                'description' => 'Pack de 10 videos con meditaciones para dormir y relajarse.',
                'type' => 'video',
                'image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=800&auto=format&fit=crop',
                'price_mxn' => 50,
                'price_usd' => 2.50,
            ],
            [
                'title' => 'Diario de Gratitud',
                'description' => 'eBook interactivo para practicar la gratitud diariamente.',
                'type' => 'ebook',
                'image' => 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?q=80&w=800&auto=format&fit=crop',
                'price_mxn' => 50,
                'price_usd' => 2.50,
            ],
        ];

        foreach ($shopItems as $item) {
            Service::updateOrCreate(
                ['title' => $item['title']],
                [
                    'description' => $item['description'],
                    'type' => $item['type'],
                    'image' => $item['image'],
                    'price' => $item['price_mxn'], // Base price
                    'price_mxn' => $item['price_mxn'],
                    'price_usd' => $item['price_usd'],
                    'duration_minutes' => 0,
                    'is_active' => true,
                ]
            );
        }
    }
}
