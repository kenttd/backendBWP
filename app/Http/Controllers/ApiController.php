<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    // Assuming you have a 'getPosts' method in your ApiController
    public function getPosts()
    {
        $dummyPosts = [
            [
                'id' => 1,
                'title' => 'Lorem Ipsum',
                'content' => 'Dolor sit amet consectetur adipiscing elit.',
                'created_at' => '2023-01-01T12:00:00Z',
            ],
            [
                'id' => 2,
                'title' => 'Sample Post',
                'content' => 'This is just a dummy post for testing purposes.',
                'created_at' => '2023-01-02T08:30:00Z',
            ],
            // Add more dummy posts as needed
        ];

        return response()->json(['posts' => $dummyPosts]);
    }
}
