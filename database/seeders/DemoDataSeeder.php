<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobPosting;
use App\Models\BlogPost;
use App\Models\Insight;
use App\Models\Application;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to truncate
        Schema::disableForeignKeyConstraints();
        JobPosting::truncate();
        BlogPost::truncate();
        Insight::truncate();
        Application::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Create 3 Jobs
        $jobs = [];
        $jobData = [
            ['title' => 'Senior Frontend Developer', 'department' => 'Engineering', 'location' => 'Addis Ababa, Ethiopia', 'type' => 'Full-time', 'experience' => '5+ Years'],
            ['title' => 'Financial Analyst', 'department' => 'Finance', 'location' => 'Remote', 'type' => 'Contract', 'experience' => '3+ Years'],
            ['title' => 'HR Operations Manager', 'department' => 'Human Resources', 'location' => 'Addis Ababa, Ethiopia', 'type' => 'Full-time', 'experience' => '7+ Years'],
        ];

        foreach ($jobData as $data) {
            $jobs[] = JobPosting::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'department' => $data['department'],
                'location' => $data['location'],
                'type' => $data['type'],
                'experience' => $data['experience'],
                'description' => 'We are looking for a ' . $data['title'] . ' to join our growing team.',
                'requirements' => ['Proven experience', 'Strong communication', 'Bachelor degree'],
                'responsibilities' => ['Daily operations', 'Team collaboration', 'Reporting'],
                'published' => true,
            ]);
        }

        // 2. Create 3 Blogs
        $blogs = [
            ['title' => 'The Future of Fintech in East Africa', 'author' => 'Abebe Bikila', 'read_time' => '5 min'],
            ['title' => 'How to Scale Your Remote Team', 'author' => 'Sara Jenkins', 'read_time' => '8 min'],
            ['title' => 'Understanding Modern Tax Regulations', 'author' => 'John Doe', 'read_time' => '12 min'],
        ];

        foreach ($blogs as $blog) {
            BlogPost::create([
                'title' => $blog['title'],
                'slug' => Str::slug($blog['title']),
                'excerpt' => 'A brief overview of ' . $blog['title'],
                'content' => 'Full content for ' . $blog['title'] . ' goes here. ' . str_repeat('Lorem ipsum dolor sit amet. ', 20),
                'author' => $blog['author'],
                'read_time' => $blog['read_time'],
                'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=60',
                'published' => true,
            ]);
        }

        // 3. Create 3 Insights
        $insights = [
            ['title' => 'Q1 Market Trends 2026', 'author' => 'Market Team', 'icon' => 'TrendingUp'],
            ['title' => 'Investing in Sustainable Energy', 'author' => 'Green Energy Dept', 'icon' => 'Leaf'],
            ['title' => 'Digital Transformation in Banking', 'author' => 'Tech Insights', 'icon' => 'Cpu'],
        ];

        foreach ($insights as $insight) {
            Insight::create([
                'title' => $insight['title'],
                'slug' => Str::slug($insight['title']),
                'excerpt' => 'Market analysis for ' . $insight['title'],
                'content' => 'Insight details for ' . $insight['title'] . '. ' . str_repeat('Analytical data points here. ', 15),
                'author' => $insight['author'],
                'read_time' => '10 min',
                'icon_name' => $insight['icon'],
                'image_url' => 'https://images.unsplash.com/photo-1551288049-bbbda5366991?w=800&auto=format&fit=crop&q=60',
                'featured' => true,
                'published' => true,
            ]);
        }

        // 4. Create 3 Job Applications (for the first job)
        $applicants = [
            ['name' => 'Alice Walker', 'email' => 'alice@example.com', 'role' => 'Software Engineer'],
            ['name' => 'Bob Smith', 'email' => 'bob@example.com', 'role' => 'Frontend Dev'],
            ['name' => 'Charlie Brown', 'email' => 'charlie@example.com', 'role' => 'React Expert'],
        ];

        foreach ($applicants as $app) {
            Application::create([
                'job_posting_id' => $jobs[0]->id,
                'fullName' => $app['name'],
                'email' => $app['email'],
                'phone' => '+251911223344',
                'location' => 'Addis Ababa',
                'currentRole' => $app['role'],
                'experience' => '5 years',
                'coverLetter' => 'I am very interested in the ' . $jobs[0]->title . ' position.',
                'resumeUrl' => 'https://example.com/resumes/sample.pdf',
                'status' => 'Pending',
            ]);
        }
    }
}
