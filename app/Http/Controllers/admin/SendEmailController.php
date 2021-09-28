<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Mail;
use Validator;

class SendEmailController extends Controller
{
        public function contactemail(){
                return view('contactemail');
        }

    public function contactemailPost(Request $request){
        $this->validate($request, [
                        'firstname' => 'required',
                        'lastname' => 'required',
                        'email' => 'required|email',
                        'comment' => 'required'
                ]);

        Mail::send('email', [
                'firstname' => $request->get('firstname'),
                'lastname' => $request->get('lastname'),
                'email' => $request->get('email'),
                'message' => $request->get('message') ],
                function ($message) {
                        $message->from('maxwell.tradeoptimize@gmail.com');
                        $message->to('osama.arshad@geeksroot.com', 'Osama Arshad')
                        ->subject('Contact Form Queries');
        });

        return back()->with('success', 'Thanks for contacting me, I will get back to you soon!');

    }
}