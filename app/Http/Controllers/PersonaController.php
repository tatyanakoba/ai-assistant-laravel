<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Inertia\Inertia;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class PersonaController extends Controller
{
    public function handlePersona(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'details' => 'required|string',
        ]);

        $name = $request->input('name');
        $details = $request->input('details');

        $prompt = "Please take on the persona of {$name}. Here are the traits and answers:\n\n{$details}\n\nRespond by listing the traits as if you are {$name}.";

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
        ]);

        $action = $request->input('action');
        $name = $request->input('persona_name');
        $details = $request->input('persona_details');

        $prompt = "Act as {$name}. Based on these traits:\n{$details}\nPlease generate {$action}.";

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

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        return Inertia::render('PersonaForm');
    }
}
