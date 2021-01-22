<?php

namespace App\Http\Controllers\Admin;

use App\Frontend;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Image;

class FrontendController extends Controller
{
    public function announceIndex()
    {
        $page_title = 'Our Announcement';
        $empty_message = 'No Announcement post yet.';
        $blog_posts = Frontend::where('key', 'blog.post')->latest()->paginate(config('constants.table.default'));
        return view('admin.frontend.blog.index', compact('page_title', 'empty_message', 'blog_posts'));
    }

    public function announceNew()
    {
        $page_title = 'New Announcement';
        return view('admin.frontend.blog.new', compact('page_title'));
    }

    public function announceEdit($id)
    {
        $page_title = 'Edit Announcement';
        $post = Frontend::where('key', 'blog.post')->where('id',$id)->firstOrFail();
        return view('admin.frontend.blog.edit', compact('page_title', 'post'));
    }
    /*
     * Our Team
     */
    public function teamIndex()
    {
        $page_title = 'Our Team';
        $empty_message = 'No Team Member Yet.';
         $caption = Frontend::where('key', 'team.caption')->latest()->firstOrFail();
        $blog_posts = Frontend::where('key', 'team')->latest()->paginate(config('constants.table.default'));
        return view('admin.frontend.team.index', compact('page_title', 'empty_message', 'blog_posts','caption'));
    }

    public function teamNew()
    {
        $page_title = 'New Team Member';
        return view('admin.frontend.team.new', compact('page_title'));
    }

    public function teamEdit($id)
    {
        $page_title = 'Update Team Member';
        $post = Frontend::where('key', 'team')->where('id',$id)->firstOrFail();
        return view('admin.frontend.team.edit', compact('page_title', 'post'));
    }


    public function seoEdit()
    {
        $page_title = 'SEO Configuration';
        $seo = Frontend::where('key', 'seo')->first();
        if (!$seo) {
            $notify[] = ['error', 'Something went wrong or not functioning properly, contact developer.'];
            return back()->withNotify($notify);
        }
        return view('admin.frontend.seo.edit', compact('page_title', 'seo'));
    }

    /*
     * Testimonial
     */

    public function testimonialIndex()
    {
        $page_title = 'Testimonials';
        $empty_message = 'No testimonials';
        $caption = Frontend::where('key', 'testimonial.caption')->latest()->firstOrFail();
        $testimonials = Frontend::where('key', 'testimonial')->latest()->paginate(config('constants.table.default'));
        return view('admin.frontend.testimonial.index', compact('page_title', 'empty_message', 'testimonials','caption'));
    }

    public function testimonialNew()
    {
        $page_title = 'New Testimonial';
        return view('admin.frontend.testimonial.new', compact('page_title'));
    }

    public function testimonialEdit($id)
    {
        $page_title = 'Edit Testimonial';
        $testi = Frontend::findOrFail($id);
        return view('admin.frontend.testimonial.edit', compact('page_title', 'testi'));
    }

    /*
     * How It Work
     */
    public function howitworkIndex()
    {
        $page_title = 'Why Choose Us';
        $empty_message = 'No Data Found';
        $caption = Frontend::where('key', 'howitwork.caption')->latest()->firstOrFail();
        $howItWorks = Frontend::where('key', 'howitwork')->latest()->paginate(config('constants.table.default'));
        return view('admin.frontend.howitwork.index', compact('page_title', 'empty_message', 'howItWorks','caption'));
    }

    public function howitworkNew()
    {
        $page_title = 'Add New Why Choose Us';
        return view('admin.frontend.howitwork.new', compact('page_title'));
    }

    public function howitworkEdit($id)
    {
        $page_title = 'Edit Why Choose Us';
        $testi = Frontend::where('id',$id)->where('key', 'howitwork')->firstOrFail();
        return view('admin.frontend.howitwork.edit', compact('page_title', 'testi'));
    }

    /*
     * WHy Choose Us
     */

    public function whychooseIndex()
    {
        $page_title = 'Services';
        $empty_message = 'No Data Found';
        $caption = Frontend::where('key', 'whychoose.caption')->latest()->firstOrFail();
        $howItWorks = Frontend::where('key', 'whychoose')->latest()->paginate(config('constants.table.default'));
        return view('admin.frontend.whychoose.index', compact('page_title', 'empty_message', 'howItWorks','caption'));
    }

    public function whychooseNew()
    {
        $page_title = 'Add New Services';
        return view('admin.frontend.whychoose.new', compact('page_title'));
    }

    public function whychooseEdit($id)
    {
        $page_title = 'Edit Services';
        $testi = Frontend::where('id',$id)->where('key', 'whychoose')->firstOrFail();
        return view('admin.frontend.whychoose.edit', compact('page_title', 'testi'));
    }


    /*
     * Flow Step
     */

    public function flowstepIndex()
    {
        $page_title = 'Process 22';
        $empty_message = 'No Data Found';
        $caption = Frontend::where('key', 'flowstep.caption')->latest()->firstOrFail();
          $howItWorks = Frontend::where('key', 'flowstep')->orderBy('id')->paginate(config('constants.table.default'));
        return view('admin.frontend.flowstep.index', compact('page_title', 'empty_message', 'howItWorks','caption'));
    }

    public function flowstepNew()
    {
        $page_title = 'Add New Process';
        return view('admin.frontend.flowstep.new', compact('page_title'));
    }

    public function flowstepEdit($id)
    {
        $page_title = 'Edit Process';
        $testi = Frontend::where('id',$id)->where('key', 'flowstep')->firstOrFail();
        $icon =  str_replace('"></i>',"",str_replace('<i class="',"",$testi->value->icon));

        return view('admin.frontend.flowstep.edit', compact('page_title', 'testi','icon'));
    }


    public function socialIndex()
    {
        $page_title = 'Social Icons';
        $empty_message = 'No social icons';
        $socials = Frontend::where('key', 'social.item')->latest()->paginate(config('constants.table.default'));
        return view('admin.frontend.social.index', compact('page_title', 'empty_message', 'socials'));
    }

    public function store(Request $request)
    {
        $validation_rule = ['key' => 'required'];
        foreach ($request->except('_token','linkedin','twitter','facebook') as $input_field => $val) {
            if ($input_field == 'has_image') {
                $validation_rule['image_input'] = ['required', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
                continue;
            }
            $validation_rule[$input_field] = 'required';
        }
        $request->validate($validation_rule, [], ['image_input' => 'image']);

        if ($request->hasFile('image_input')) {
            try {
                $request->merge(['image' => $this->store_image($request->key, $request->image_input)]);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Could not upload the Image.'];
                return back()->withNotify($notify)->withInput();
            }
        }

        Frontend::create([
            'key' => $request->key,
            'value' => $request->except('_token', 'key', 'image_input'),
        ]);

        $notify[] = ['success', 'Saved Successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        foreach ($request->except('_token','linkedin','twitter','facebook') as $input_field => $val) {
            if ($request->image_input) {
                $validation_rule['image_input'] = ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
                continue;
            }
            $validation_rule[$input_field] = 'required';
        }
        $request->validate($validation_rule, [], ['image_input' => 'image']);

        $content = Frontend::findOrFail($request->id);
        if ($request->hasFile('image_input')) {
            try {
                $request->merge(['image' => $this->store_image($content->key, $request->image_input, $content->value->image)]);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Could not upload the Image.'];
                return back()->withNotify($notify);
            }
        } else if (isset($content->value->image)) {
            $request->merge(['image' => $content->value->image]);
        }

        $content->update(['value' => $request->except('_token', 'image_input', 'key')]);
        $notify[] = ['success', 'Content has been updated.'];
        return back()->withNotify($notify);
    }

    public function remove(Request $request)
    {
        $request->validate(['id' => 'required']);
        $frontend = Frontend::findOrFail($request->id);
        if(isset($frontend->value->image)) {
            remove_file( config('constants.frontend.team.path').'/'. $frontend->value->image);
        }
        $frontend->delete();
        $notify[] = ['success', 'Content has been removed.'];
        return back()->withNotify($notify);
    }

    protected function store_image($key, $image, $old_image = null)
    {
        $path = config('constants.frontend.' . $key . '.path');
        $size = config('constants.frontend.' . $key . '.size');
        $thumb = config('constants.frontend.' . $key . '.thumb');
        return upload_image($image, $path, $size, $old_image, $thumb);
    }




    public function faqIndex()
    {
        $page_title = 'FAQ';
        $empty_message = 'No FAQ create yet.';
        $blog_posts = Frontend::where('key', 'faq')->latest()->paginate(config('constants.table.default'));
        return view('admin.frontend.faq.index', compact('page_title', 'empty_message', 'blog_posts'));
    }

    public function faqNew()
    {
        $page_title = 'Add New FAQ';
        return view('admin.frontend.faq.new', compact('page_title'));
    }

    public function faqEdit($id)
    {
        $page_title = 'Edit FAQ';
        $post = Frontend::findOrFail($id);
        return view('admin.frontend.faq.edit', compact('page_title', 'post'));
    }



    public function companyPolicy()
    {
        $page_title = 'Company Policy';
        $empty_message = 'No Policy yet.';
        $blog_posts = Frontend::where('key', 'company_policy')->latest()->paginate(config('constants.table.default'));
        return view('admin.frontend.policy.index', compact('page_title', 'empty_message', 'blog_posts'));
    }

    public function companyPolicyNew()
    {
        $page_title = 'Add New Policy';
        return view('admin.frontend.policy.new', compact('page_title'));
    }

    public function companyPolicyEdit($id)
    {
        $page_title = 'Edit Policy';
        $post = Frontend::findOrFail($id);
        return view('admin.frontend.policy.edit', compact('page_title', 'post'));
    }


    public function menu()
    {
        $page_title = 'Manage Menu';
        $empty_message = 'No Menu yet.';
        $blog_posts = Frontend::where('key', 'menu')->latest()->paginate(config('constants.table.default'));
        return view('admin.frontend.menu.index', compact('page_title', 'empty_message', 'blog_posts'));
    }

    public function menuNew()
    {
        $page_title = 'Add New Menu';
        return view('admin.frontend.menu.new', compact('page_title'));
    }

    public function menuEdit($id)
    {
        $page_title = 'Edit Menu';
        $post = Frontend::findOrFail($id);
        return view('admin.frontend.menu.edit', compact('page_title', 'post'));
    }


    public function sectionContact()
    {
        $page_title = 'Manage Contact';
        $post = Frontend::where('key','contact')->firstOrFail();
        return view('admin.frontend.section.contact', compact('page_title', 'post'));
    }
    public function sectionTransaction()
    {
        $page_title = 'Call To Action';
        $post = Frontend::where('key','transection')->firstOrFail();
        return view('admin.frontend.section.transection', compact('page_title', 'post'));
    }
    public function homeContent()
    {
        $page_title = 'Home Content';
        $post = Frontend::where('key','homecontent')->firstOrFail();
        return view('admin.frontend.section.homecontent', compact('page_title', 'post'));
    }



    public function sectionAbout()
    {
        $page_title = 'Manage About';
        $post = Frontend::where('key','about')->firstOrFail();
        return view('admin.frontend.section.about', compact('page_title', 'post'));
    }

    public function sectionAboutUpdate(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'sub_title' => 'required',
            'details' => 'required',
            'image' => ' mimes:jpeg,jpg,png| max:2048',
            'signature' => ' mimes:png| max:2048'
        ]);
        $data = Frontend::where('id',$id)->where('key','about')->firstOrFail();

        $in['title'] = $request->title;
        $in['sub_title'] = $request->sub_title;
        $in['details'] = $request->details;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = 'about.jpg';
            $location = 'assets/images/frontend/about/' . $filename;
            Image::make($image)->save($location);
            $in['image'] = $filename;
        }
        if($request->hasFile('signature')){
            $image = $request->file('signature');
            $filename = 'signature.png';
            $location = 'assets/images/frontend/about/' . $filename;
            Image::make($image)->resize(180,68)->save($location);
            $in['signature'] = $filename;
        }

        $data->value = $in;
        $data->save();


        $notify[] = ['success', 'Update Successfully.'];
        return back()->withNotify($notify);
    }


    public function sectionDeveloper()
    {
        $page_title = 'Developer Section';
        $post = Frontend::where('key','developer')->firstOrFail();
        return view('admin.frontend.section.developer', compact('page_title', 'post'));
    }



}
