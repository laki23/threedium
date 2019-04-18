<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Article;
use App\Picture;
use Illuminate\Support\Facades\DB;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = DB::table('articles')
        ->join ('pictures', 'pictures.article_id', '=', 'articles.id')
        ->where ('pictures.status', 1)
        ->get();

        return response() -> json ($articles);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $article=new Article;
        $pictures=new Picture;
        $this->validate($request, [
            'title' => 'required',
            'text' => 'required',
            'picture' => 'required|image|max:2048'
        ]);
        
        $userId = Auth::user()->id;
        $article->title = $request->title;
        $article->text = $request->text;
        $article->user_id = $userId;
        $article->save();

        // VADIM ID NEOPHODAN ZA FOLDER SA SLIKAMA
        $article_id=Article::max('id');
        // PRAVIM FOLDER U KOM CU KASNIJE CUVATI SLIKE
        File::makeDirectory('articles/'.$article_id.'/', 0711, true, true); 
        for($i=0;$i<$request->input('picSum');$i++){
            // UZIMAM NAZIV DA BIH DOBIO EKSTENZIJU
            $picture=$request->file('picture-'.$i)->getClientOriginalName();
            // UZIMAM EKSTENZIJU
            $ext = pathinfo($picture, PATHINFO_EXTENSION);
            // NAZIV FAJLA KAKO GA CUVAM
            $name=$i.'.'.$ext;
            // CUVAM SLIKU U FOLDER
            $path = $request->file('picture-'.$i)->storeAs('public/articles/'.$article_id, $name);
            
            //POPUNJAVAM POLJA U BAZI
            
            $pictures->picture=$name;
            $pictures->article_id=$article_id;


            // POSTAVLJAM STATUS SLIKE - GLAVNU SLIKU OGLASA
             $i==$request->input('glavnaSlika') ? $pictures->status=1 : $pictures->status=0;

            // CUVAM SLIKU U BAZU
            $pictures->save();
        }
        $response=array('status'=>true);
        return response()->json($pictures,$article);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $articles = DB::table('articles')
        ->join ('pictures', 'pictures.article_id', '=', 'articles.id')
        ->where ('pictures.status', 1)
        ->where ('articles.id',$id)
        ->get();

        return response() -> json ($articles);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $pictures = Picture::where('article_id','=',$id)->get();
        foreach($pictures as $pic){
            $pic->delete();
        }
        $article=Article::find($id);
        $article->delete();
        File::deleteDirectory('storage/articles/'.$id);
        $response=array('status'=>true);
        return response()->json($response);
    }
    public function userPosts()
    {
        $userId = Auth::user()->id;
        $article = DB::table('articles')
        ->join ('pictures', 'pictures.article_id', '=', 'articles.id')
        ->where ('pictures.status', 1)
        ->where ('articles.user_id', $userId)
        ->get();
        return response()->json($article);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
}
