<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Insight;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SchedulingController extends Controller
{
    public function sync(Request $request)
    {
        $now = Carbon::now();

        // 1. Sync Blog Posts
        $postsCount = BlogPost::where('published', false)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->update([
                'published' => true,
                'scheduled_at' => null,
            ]);

        // 2. Sync Insights
        $insightsCount = Insight::where('published', false)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->update([
                'published' => true,
                'scheduled_at' => null,
            ]);

        // 3. Sync Job Postings
        $jobsCount = JobPosting::where('published', false)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->update([
                'published' => true,
                'scheduled_at' => null,
            ]);

        return response()->json([
            'message' => 'Successfully synced scheduled content.',
            'posts_published' => $postsCount,
            'insights_published' => $insightsCount,
            'jobs_published' => $jobsCount,
        ]);
    }
}
