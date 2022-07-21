<?php

declare (strict_types=1);

namespace App\Http\Controllers;

use App\Book;
use App\BookReview;
use App\Http\Requests\PostBookRequest;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\BookGetCollectionRequest;
class BooksController extends Controller
{
    public function getCollection(BookGetCollectionRequest $request)
    {
        $request->validated();
       
        
        $books = $this->getAllBooksPaginate($request);
        return BookResource::collection($books);
    }
    
    protected function getAllBooksPaginate($request)
    {
        $title = $request->get('title',false);
        $authorsId = false;
        $sortColumn = $request->get('sortColumn', false);
        $sortDirection = $request->get('sortDirection', false);

        $isDescSorting =  $sortDirection == 'DESC' ? true : false;
        $isAvgReview = $sortColumn == 'avg_review';

        if ($request->has('authors')) {
            if(str_contains($request->authors , ',')){
                $authorsId = explode(',', $request->authors);
            }else{
                $authorsId = [$request->authors];  
            }
        }
     
        
        $baseQuery = Book::when($authorsId , function($query, $authorsId){
            $query->whereHas('authors', function($query) use ($authorsId) {
                $query->whereIntegerInRaw('id', $authorsId);
            });

            },
            function($query){
                $query->with('authors');
            })
        ->when($title, function ($query, $title) {
             $query->where('title',$title)->orWhere('title', 'like', '%' . $title . '%');
        })
        ->with('reviews');
     
        
        $paginator = $baseQuery->paginate();
        if ($sortColumn ){
            $paginator = $paginator->getCollection()->sortBy(
                function ($book, $key) use($isAvgReview) {
                    if($isAvgReview){
                        return $book->reviews->avg('review'); 
                    }                
                    return $book['title'];
                },
                SORT_REGULAR,
                $isDescSorting);

        }
        
        
 
        return $paginator;

        
    }

    public function post(PostBookRequest $request)
    {
        $request->validated();

        $book = Book::create($request->only('isbn', 'title' ,'description'));
        
        foreach ($request->only('authors') as $key => $authorId) {
            $book->authors()->attach($authorId);
        }
        
     
        return response()->json([
            'data'=>new BookResource($book)
        ],201);
       
    }

    public function postReview(Book $book, PostBookReviewRequest $request)
    {
        $request->validated();
        $attributes = $request->only('review','comment' );
        $attributes['book_id'] = $book->id;
        $attributes['user_id'] = auth()->user()->id;
        $bookReview = BookReview::create($attributes);
       
        return response()->json([
            'data'=>new BookReviewResource($bookReview)
        ],201);
    }
}
