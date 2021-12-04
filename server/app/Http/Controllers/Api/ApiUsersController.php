<?php

namespace App\Http\Controllers\Api;

use App\Models\Inventory;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApiUsersController extends ApiController
{
    // Api users index route
    public function index(Request $request)
    {
        $users = $this->getItems(User::class, User::select(), $request)
            ->orderByRaw('active DESC, LOWER(lastname)')
            ->paginate($this->getLimit($request))->withQueryString();
        if ($request->user()->role != User::ROLE_MANAGER && $request->user()->role != User::ROLE_ADMIN) {
            $users = $users->where('active', true);
        }
        for ($i = 0; $i < $users->count(); $i++) {
            $users[$i] = $users[$i]->toApiData($request->user());
        }
        return $users;
    }

    // Api users show route
    public function show(Request $request, User $user)
    {
        return $user->toApiData($request->user());
    }

    // Api users show notifcations route
    public function showNotifications(Request $request, User $user)
    {
        $notifications = $request->user()->notifications()
            ->paginate($this->getLimit($request))->withQueryString();
        for ($i = 0; $i < $notifications->count(); $i++) {
            $notifications[$i] = Notification::toApiData($notifications[$i], $request->user());
        }
        return $notifications;
    }

    // Api users show unread notifcations route
    public function showUnreadNotifications(Request $request, User $user)
    {
        $notifications = $request->user()->unreadNotifications()
            ->paginate($this->getLimit($request))->withQueryString();
        for ($i = 0; $i < $notifications->count(); $i++) {
            $notifications[$i] = Notification::toApiData($notifications[$i], $request->user());
        }
        return $notifications;
    }

    // Api users show posts route
    public function showPosts(Request $request, User $user)
    {
        $posts = $this->getItems(Post::class, $user->posts(), $request)
            ->orderBy('created_at', 'DESC')
            ->paginate($this->getLimit($request))->withQueryString();
        for ($i = 0; $i < $posts->count(); $i++) {
            $posts[$i] = $posts[$i]->toApiData($request->user());
        }
        return $posts;
    }

    // Api users show inventories route
    public function showInventories(Request $request, User $user)
    {
        $inventories = $this->getItems(Inventory::class, $user->inventories(), $request)
            ->orderBy('created_at', 'DESC')
            ->paginate($this->getLimit($request))->withQueryString();
        for ($i = 0; $i < $inventories->count(); $i++) {
            $inventories[$i] = $inventories[$i]->toApiData($request->user(), ['products']);
        }
        return $inventories;
    }

    // Api users show transactions route
    public function showTransactions(Request $request, User $user)
    {
        $transactions = $this->getItems(Transaction::class, $user->transactions(), $request)
            ->orderBy('created_at', 'DESC')
            ->paginate($this->getLimit($request))->withQueryString();
        for ($i = 0; $i < $transactions->count(); $i++) {
            $transactions[$i] = $transactions[$i]->toApiData($request->user(), [
                'user', // For backwards compatability
                'products'
            ]);
        }
        return $transactions;
    }

    // Api users check balances route
    public function checkBalances()
    {
        User::checkBalances();
        return [
            'message' => 'User balances are checked'
        ];
    }

    // Api users edit route
    public function edit(Request $request, User $user)
    {
        // Validate input
        $rules = [
            'firstname' => 'nullable|min:2|max:48',
            'insertion' => 'nullable|max:16',
            'lastname' => 'nullable|min:2|max:48',
            'gender' => [
                'nullable',
                Rule::in(['null', 'male', 'female', 'other'])
            ],
            'birthday' => 'nullable|date',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->email, 'email')
            ],
            'phone' => 'nullable|max:255',
            'address' => 'nullable|min:2|max:255',
            'postcode' => 'nullable|min:2|max:32',
            'city' => 'nullable|min:2|max:255',
            'language' => 'nullable|integer|digits_between:' . User::LANGUAGE_ENGLISH . ',' . User::LANGUAGE_DUTCH,
            'theme' => 'nullable|integer|digits_between:' . User::THEME_LIGHT . ',' . User::THEME_DARK,
            'receive_news' => 'nullable|boolean'
        ];
        if ($request->input('avatar') && $request->input('avatar') != 'null') {
            $rules['avatar'] = 'required|image|mimes:jpg,jpeg,png|max:1024';
        }
        if ($request->input('thanks') && $request->input('thanks') != 'null') {
            $rules['thanks'] = 'required|image|mimes:gif|max:2048';
        }
        if ($request->input('current_password')) {
            $rules['current_password'] = 'required|current_password';
            $rules['password'] = 'required|min:6';
            $rules['password_confirmation'] = 'required|same:password';
        }
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response(['errors' => $validation->errors()], 400);
        }

        // Update details
        if ($request->input('firstname')) {
            $user->firstname = $request->input('firstname');
        }

        if ($request->input('insertion')) {
            if ($request->input('insertion') == 'null') {
                $user->insertion = null;
            } else {
                $user->insertion = $request->input('insertion');
            }
        }

        if ($request->input('lastname')) {
            $user->lastname = $request->input('lastname');
        }

        if ($request->input('gender')) {
            if ($request->input('gender') == 'null') $user->gender = null;
            if ($request->input('gender') == 'male') $user->gender = User::GENDER_MALE;
            if ($request->input('gender') == 'female') $user->gender = User::GENDER_FEMALE;
            if ($request->input('gender') == 'other') $user->gender = User::GENDER_OTHER;
        }

        if ($request->input('birthday')) {
            if ($request->input('birthday') == 'null') {
                $user->birthday = null;
            } else {
                $user->birthday = $request->input('birthday');
            }
        }

        if ($request->input('email')) $user->email = $request->input('email');

        if ($request->input('phone')) {
            if ($request->input('phone') == 'null') {
                $user->phone = null;
            } else {
                $user->phone = $request->input('phone');
            }
        }

        if ($request->input('address')) {
            if ($request->input('address') == 'null') {
                $user->address = null;
            } else {
                $user->address = $request->input('address');
            }
        }

        if ($request->input('postcode')) {
            if ($request->input('postcode') == 'null') {
                $user->postcode = null;
            } else {
                $user->postcode = $request->input('postcode');
            }
        }

        if ($request->input('city')) {
            if ($request->input('city') == 'null') {
                $user->city = null;
            } else {
                $user->city = $request->input('city');
            }
        }

        if ($request->input('language')) {
            if ($request->input('language') == 'en') $user->language = User::LANGUAGE_ENGLISH;
            if ($request->input('language') == 'nl') $user->language = User::LANGUAGE_DUTCH;
        }

        if ($request->input('theme')) {
            if ($request->input('theme') == 'light') $user->theme = User::THEME_LIGHT;
            if ($request->input('theme') == 'dark') $user->theme = User::THEME_DARK;
        }

        if ($request->input('receive_news')) {
            $user->receive_news = $request->input('receive_news') == 'true';
        }

        // Update avatar
        if ($request->input('avatar')) {
            if ($request->input('avatar') == 'null') {
                // Delete user avatar file from storage
                Storage::delete('public/avatars/' . $user->avatar);

                // Update user that he has no avatar
                $user->avatar = null;
            } else {
                // Save file to avatars folder
                $avatarName = User::generateAvatarName($this->avatar->extension());
                $this->avatar->storeAs('public/avatars', $avatarName);

                // Delete old user avatar
                if ($user->avatar != null) {
                    Storage::delete('public/avatars/' . $user->avatar);
                }

                // Update user that he has an avatar
                $user->avatar = $avatarName;
            }
        }

        // Update thanks
        if ($request->input('thanks')) {
            if ($request->input('thanks') == 'null') {
                // Delete user thanks file from storage
                Storage::delete('public/thanks/' . $user->thanks);

                // Update user that he has no thanks
                $user->thanks = null;
            } else {
                // Save file to thanks folder
                $thanksName = User::generateThanksName($this->thanks->extension());
                $this->thanks->storeAs('public/thanks', $thanksName);

                // Delete old user thanks
                if ($user->thanks != null) {
                    Storage::delete('public/thanks/' . $user->thanks);
                }

                // Update user that he has an thanks
                $user->thanks = $thanksName;
            }
        }

        // Update password
        if ($request->input('current_password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Save and send response message
        $user->save();
        return [
            'message' => 'All user changes are saved!',
            'user' => $user
        ];
    }
}
