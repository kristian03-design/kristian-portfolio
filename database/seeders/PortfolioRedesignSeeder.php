<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Experience;

class PortfolioRedesignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Projects
        $projects = [
            [
                'title' => 'GBLDC Mobile Loan Application',
                'description' => 'A comprehensive mobile solution for managing loan applications, featuring real-time status tracking and secure document uploads.',
                'image_path' => '/images/projects/gbldc.png',
                'url' => '#',
                'github_url' => 'https://github.com',
                'tech_stack' => ['Flutter', 'Firebase'],
                'order' => 1,
            ],
            [
                'title' => 'BTECH Admission System',
                'description' => 'An automated system for handling student applications, entrance exams, and enrollment workflows for a technical college.',
                'image_path' => '/images/projects/btech.png',
                'url' => '#',
                'github_url' => 'https://github.com',
                'tech_stack' => ['Laravel', 'MySQL'],
                'order' => 2,
            ],
            [
                'title' => 'OJT Tracker System',
                'description' => 'A logbook and performance monitoring system for interns, streamlining the documentation of hours and tasks completed.',
                'image_path' => '/images/projects/ojt.png',
                'url' => '#',
                'github_url' => 'https://github.com',
                'tech_stack' => ['PHP', 'JavaScript'],
                'order' => 3,
            ],
            [
                'title' => 'Flutter Banking UI Concept',
                'description' => 'A high-fidelity mobile banking concept focusing on glassmorphism, smooth animations, and intuitive transaction flows.',
                'image_path' => '/images/projects/banking.png',
                'url' => '#',
                'github_url' => 'https://github.com',
                'tech_stack' => ['Flutter', 'UI Design'],
                'order' => 4,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(['title' => $project['title']], $project);
        }

        // Skills
        $skills = [
            ['name' => 'HTML', 'category' => 'Frontend', 'proficiency_level' => 95],
            ['name' => 'CSS', 'category' => 'Frontend', 'proficiency_level' => 90],
            ['name' => 'JavaScript', 'category' => 'Frontend', 'proficiency_level' => 85],
            ['name' => 'Tailwind CSS', 'category' => 'Frontend', 'proficiency_level' => 95],
            ['name' => 'Flutter', 'category' => 'Frontend', 'proficiency_level' => 80],
            ['name' => 'PHP', 'category' => 'Backend', 'proficiency_level' => 90],
            ['name' => 'Laravel', 'category' => 'Backend', 'proficiency_level' => 85],
            ['name' => 'MySQL', 'category' => 'Backend', 'proficiency_level' => 85],
            ['name' => 'REST API', 'category' => 'Backend', 'proficiency_level' => 80],
            ['name' => 'GitHub', 'category' => 'Tools', 'proficiency_level' => 90],
            ['name' => 'Vercel', 'category' => 'Tools', 'proficiency_level' => 80],
            ['name' => 'Firebase', 'category' => 'Tools', 'proficiency_level' => 75],
            ['name' => 'Supabase', 'category' => 'Tools', 'proficiency_level' => 70],
            ['name' => 'Figma', 'category' => 'Tools', 'proficiency_level' => 75],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(['name' => $skill['name']], $skill);
        }

        // Experiences
        $experiences = [
            [
                'role' => 'Entry-Level Developer',
                'company' => 'Freelance & Personal Projects',
                'description' => 'Focusing on Laravel and Flutter development. Building real-world systems like GBLDC and OJT Trackers to solve specific operational challenges.',
                'start_date' => '2023-01-01',
                'is_current' => true,
            ],
            [
                'role' => 'Web Development Intern',
                'company' => 'BTECH Admission Office',
                'description' => 'Developed the Admission Office System using Laravel. Focused on automating student intake and exam scheduling workflows.',
                'start_date' => '2023-06-01',
                'end_date' => '2023-12-31',
                'is_current' => false,
            ],
        ];

        foreach ($experiences as $exp) {
            Experience::updateOrCreate(['role' => $exp['role'], 'company' => $exp['company']], $exp);
        }
    }
}
