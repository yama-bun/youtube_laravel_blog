<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Http\Requests\BlogRequest;
use PhpParser\Node\Stmt\TryCatch;

class BlogController extends Controller
{
    /**
     * ブログ一覧を表示する
     * @return view
     */

    public function showList()
    {
        $blogs = Blog::paginate(15);

        return view('blog.list',compact('blogs'));

    }
    /**
     * ブログ登録画面を表示
     * @return view
     */
    public function showCreate()
    {
        return view('blog.form');
    }

    /**
     * ブログ登録機能
     */
    public function exeStore(BlogRequest $request)
    {
        $inputs = $request->all();

        \DB::beginTransaction();
        try {

            Blog::create($inputs);
            \DB::commit();
        } catch(\Throwable $e) {
            \DB::rollback();
            abort(500);
        }


        \Session::flash('err_msg', '登録が完了しました。');

        return redirect(route('blogs'));
    }

    /**
     * ブログ詳細を表示する
     * @param int $id
     * @return view
     */
    public function showDetail($id)
    {
        $blog = Blog::find($id);

        if (is_null($blog)) {
            \Session::flash('err_msg', 'データがありません');
            return redirect(route('blogs'));
        }

        return view('blog.detail', compact('blog'));
    }

    public function edit($id)
    {
        $blog = Blog::find($id);

        if (is_null($blog)) {
            \Session::flash('err_msg', 'データがありません');
            return redirect(route('blogs'));
        }

        return view('blog.edit', compact('blog'));
    }

    public function update(BlogRequest $request)
    {
        $inputs = $request->all();
        // dd($inputs);

        \DB::beginTransaction();
        try {
            $blog = Blog::find($inputs['id']);
            $blog->fill([
                'title' => $inputs['title'],
                'content' => $inputs['content'],
            ]);
            $blog->save();
            \DB::commit();
        } catch(\Throwable $e) {
            \DB::rollback();
            abort(500);
        }
        \Session::flash('err_msg', 'ブログを更新しました。');
        return redirect(route('blogs'));
    }

    public function delete($id)
    {
        if (empty($id)) {
            \Session::flash('err_msg', 'データがありません');
            return redirect(route('blogs'));
        }
        try {
            Blog::destroy($id);
        } catch(\Throwable $e) {
            abort(500);
        }
        \Session::flash('err_msg', '削除しました');

        return redirect(route('blogs'));

    }

}
