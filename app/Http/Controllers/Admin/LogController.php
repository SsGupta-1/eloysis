<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class LogController extends Controller
{
    protected string $logDirectory;

    public function __construct()
    {
        $this->logDirectory = storage_path('logs');
    }

    /**
     * Log viewer page.
     */
    public function index()
    {
        return view('admin.logs.index');
    }

    /**
     * Get available log files.
     */
    public function files()
    {
        $files = collect(
            File::files($this->logDirectory)
        )
            ->filter(function ($file) {

                return preg_match(
                    '/^laravel-\d{4}-\d{2}-\d{2}\.log$/',
                    $file->getFilename()
                );
            })
            ->sortByDesc(function ($file) {

                return $file->getMTime();
            })
            ->values()
            ->map(function ($file) {

                return [
                    'name' => $file->getFilename(),

                    'date' => preg_replace(
                        '/^laravel-(\d{4}-\d{2}-\d{2})\.log$/',
                        '$1',
                        $file->getFilename()
                    ),

                    'size' => $file->getSize(),
                ];
            });

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    /**
     * Read selected log.
     */
    // public function read(Request $request)
    // {
    //     $request->validate([
    //         'file' => [
    //             'required',
    //             'string',
    //             'regex:/^laravel-\d{4}-\d{2}-\d{2}\.log$/',
    //         ],

    //         'lines' => [
    //             'nullable',
    //             'integer',
    //             'in:100,500,1000,2000,5000',
    //         ],

    //         'search' => [
    //             'nullable',
    //             'string',
    //             'max:200',
    //         ],
    //     ]);

    //     $filename = $request->input('file');

    //     $lines = (int) $request->input('lines', 1000);

    //     $search = trim(
    //         $request->input('search', '')
    //     );

    //     $path = $this->safePath($filename);

    //     if (!File::exists($path)) {

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Log file not found.',
    //         ], 404);
    //     }

    //     $content = $this->getLogLines(
    //         $path,
    //         $lines,
    //         $search
    //     );

    //     return response()->json([
    //         'success' => true,
    //         'file' => $filename,
    //         'lines' => $lines,
    //         'search' => $search,
    //         'content' => $content,
    //     ]);
    // }

    public function read(Request $request)
    {
        $file = $request->get('file');

        $limit = min(
            max((int) $request->get('lines', 1000), 1),
            1000
        );

        $search = trim((string) $request->get('search', ''));

        if (! $file || ! preg_match('/^laravel-\d{4}-\d{2}-\d{2}\.log$/', $file)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid log file.',
            ], 400);
        }

        $path = $this->logDirectory.DIRECTORY_SEPARATOR.$file;

        if (! File::exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Log file not found.',
            ], 404);
        }

        $content = File::get($path);

        /*
        |--------------------------------------------------------------------------
        | Split log entries
        |--------------------------------------------------------------------------
        */

        $entries = preg_split(
            '/(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/',
            trim($content)
        );

        $entries = array_values(
            array_filter(
                $entries,
                fn ($entry) => trim($entry) !== ''
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Latest 1000 entries only
        |--------------------------------------------------------------------------
        */

        $entries = array_slice($entries, -$limit);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $entries = array_values(
                array_filter(
                    $entries,
                    fn ($entry) => stripos($entry, $search) !== false
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Newest first
        |--------------------------------------------------------------------------
        */

        $entries = array_reverse($entries);

        return response()->json([
            'success' => true,
            'file' => $file,
            'lines' => count($entries),
            'logs' => $entries,
        ]);
    }

    /**
     * Download selected log.
     */
    public function download(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'string',
                'regex:/^laravel-\d{4}-\d{2}-\d{2}\.log$/',
            ],
        ]);

        $filename = $request->input('file');

        $path = $this->safePath($filename);

        abort_unless(
            File::exists($path),
            404
        );

        return response()->download(
            $path,
            $filename,
            [
                'Content-Type' => 'text/plain',
            ]
        );
    }

    /**
     * Secure log path.
     */
    protected function safePath(string $filename): string
    {
        if (! preg_match(
            '/^laravel-\d{4}-\d{2}-\d{2}\.log$/',
            $filename
        )) {
            abort(404);
        }

        $basePath = realpath($this->logDirectory);

        $path = realpath(
            $this->logDirectory.DIRECTORY_SEPARATOR.$filename
        );

        if (
            ! $basePath ||
            ! $path ||
            ! str_starts_with(
                $path,
                $basePath.DIRECTORY_SEPARATOR
            )
        ) {
            abort(404);
        }

        return $path;
    }

    /**
     * Get last N lines efficiently.
     */
    protected function getLogLines(
        string $path,
        int $limit,
        string $search = ''
    ): string {

        $file = new \SplFileObject($path, 'r');

        $buffer = [];

        while (! $file->eof()) {

            $line = $file->fgets();

            if ($search !== '') {

                if (
                    stripos(
                        $line,
                        $search
                    ) === false
                ) {
                    continue;
                }
            }

            $buffer[] = rtrim(
                $line,
                "\r\n"
            );

            if (count($buffer) > $limit) {
                array_shift($buffer);
            }
        }

        return implode(
            PHP_EOL,
            $buffer
        );
    }
}
