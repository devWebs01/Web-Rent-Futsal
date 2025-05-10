<?php

namespace App\Http\Controllers;

use App\Models\Blog;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->delete();

            return redirect()->route('blogs.index')
                ->with('success', 'Data blog berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Gagal menghapus blog: '.$e->getMessage());

            return redirect()->route('blogs.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data blog.');
        }
    }
}
