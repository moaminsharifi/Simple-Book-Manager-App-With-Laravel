<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        // @todo: update it with better laravel way
        $authors = [];
        if ($this->authors) {
            foreach ($this->authors as $author) {
                $authors[] = new AuthorResource($author);
            }
        }
        $avgRating = $this->reviews->avg('review');

        return [

            'id' => $this->id,
            'isbn' => $this->isbn,
            'title' => $this->title,
            'description' => $this->description,
            'authors'=>$authors,
            'review'=>[
                'avg'=>is_null($avgRating) ? 0 : round($avgRating),
                'count'=>$this->reviews->count(),
            ],

        ];
    }
}
