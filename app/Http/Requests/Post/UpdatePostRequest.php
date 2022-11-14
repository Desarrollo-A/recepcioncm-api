<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\PostDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest implements ReturnDtoInterface
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id'    => ['required', 'integer'],
            'title' => ['required', 'min:5'],
            'body'  => ['required', 'max:20']
        ];
    }
    public function toDTO(): PostDTO
    {
        return new PostDTO([
            'id'    =>  $this->id,
            'title'  => $this->title,
            'body'  =>  $this->body
        ]);
    }
}
