<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Post\PostRepositoryInterface;
use App\Backend\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller {
    protected $postRepository;

    public function __construct(PostRepositoryInterface $repos) {
        $this->postRepository = $repos;
    }

    public function index() {
       return Auth::user();
        $user = Post::all();
        dd($user);
//        dd();
//        $flight = new Post();
//        dd($flight);
//        $post = Post::all();
//        dd('test');
        return $this->postRepository->getAllUser();
    }
}

?>