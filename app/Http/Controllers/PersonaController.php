<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class PersonaController extends Controller
{
    public function handlePersona(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'part1' => 'required|string',
            'part2' => 'required|string',
            'part3' => 'required|string',
        ]);

        $name = $request->input('name');
        $part1 = $request->input('part1');
        $part2 = $request->input('part2');
        $part3 = $request->input('part3');

        $fullDetails = <<<EOD
Part 1: Market / Product / Pain Points
{$part1}

Part 2: Demographics
{$part2}

Part 3: Psychographics
{$part3}
EOD;

        $prompt = <<<EOT
You are now the persona named "{$name}".

I will give you several pieces of information in sequence.

{$fullDetails}

Now, summarize all the information above as if you are {$name}, using the first person ("I").
Then ask: "What would you like to do next?"
Options: Create ads, Create keywords, Create display URLs, Create callouts, Create extensions, Create sitelinks, Create audience.
EOT;

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert marketing strategist.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return response()->json([
                'formatted' => $response['choices'][0]['message']['content'],
            ]);

        } catch (\Exception $e) {
            Log::error('OpenAI handlePersona Error: ' . $e->getMessage());
            return response()->json(['error' => 'AI error: ' . $e->getMessage()], 500);
        }
    }

    public function generateAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'persona_name' => 'required|string',
            'persona_details' => 'required|string',
            'specifics' => 'nullable|string',
        ]);

        $action = $request->input('action');
        $name = $request->input('persona_name');
        $details = $request->input('persona_details');
        $specifics = $request->input('specifics');

        $specificsText = $specifics
            ? "\n\nThe user has provided additional instructions for this action:\n{$specifics}"
            : '';

        $prompt = "Act as {$name}. Based on these traits:\n{$details}\nPlease generate {$action}.{$specificsText}";

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert marketing strategist.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return response()->json([
                'result' => $response['choices'][0]['message']['content'],
            ]);

        } catch (\Exception $e) {
            Log::error('OpenAI generateAction Error: ' . $e->getMessage());
            return response()->json(['result' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show()
    {
        return view('assistant');
    }
}
