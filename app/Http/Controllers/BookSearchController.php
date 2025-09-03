<?php


namespace App\Http\Controllers;

use App\Services\GoogleBooksService;
use Illuminate\Http\Request;

class BookSearchController extends Controller
{
    protected $googleBooksService;

    public function __construct(GoogleBooksService $googleBooksService)
    {
        $this->googleBooksService = $googleBooksService;
    }

    public function index()
    {
        $defaultQuery = 'best sellers'; 
        $results = $this->googleBooksService->searchBooks($defaultQuery);
        
        return view('books.search', ['results' => $results, 'query' => null]);
    }

    
    public function search(Request $request)
    {
        $query = $request->input('q');
        $results = $this->googleBooksService->searchBooks($query);

        return view('books.search', ['results' => $results, 'query' => $query]);
    }
}


