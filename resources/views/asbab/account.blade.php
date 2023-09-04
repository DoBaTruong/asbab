@extends('asbab.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('guest/assets/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/slider/slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/account/account.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('guest/assets/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('guest/slider/slider.js') }}"></script>
    <script src="{{ asset('guest/account/account.js') }}"></script>
@endsection

@section('content')
    <main>
        <section class="breadcrumb-area">
            <div class="container">
                <div class="row">
                    <nav class="breadcrumb">
                        <a class="breadcrumb-item" href="{{ route('asbab.home') }}">Home</a>
                        <span class="breadcrumb-item active">My Account</span>
                    </nav>
                </div>
            </div>
        </section>
        <section class="account-area">
            <div class="container">
                <div class="row mb-5 justify-content-end">
                    <a href="{{ route('asbab.order.index') }}" class="btn btn-dark mr-2"><i class="fa fa-history"></i> Orders history</a>
                    <a href="{{ route('asbab.wishlist') }}" class="btn btn-success"><i class="fa fa-heart"></i> Wishlist</a>
                </div>
                <div class="row acc-group">
                    <div class="acc-head mb-3">
                        <h3>My Account<span class="edit-badge btn-edit-profile"><i class="fa fa-edit"></i></span></h3>
                        <p>Manage profile information for account security.</p>
                    </div>
                    <form method="post" class="w-100" id="profile" data-action="{{ route('asbab.account.edit_profile') }}">
                        @csrf
                        <table class="table acc-table">
                            <tbody>
                                <tr>
                                    <td>Full Name</td>
                                    <td>:</td>
                                    <td><input class="form-control" disabled type="text" name="pf_name"
                                            value="{{ auth()->user()->name }}" />
                                    </td>
                                    <td rowspan="4" id="userava">
                                        <div class="pro-ava">
                                            <div class="avar-container">
                                                <div id="preview-ava-profile">
                                                    <img src="{{ asset(auth()->user()->avatar) }}" alt="" />
                                                </div>
                                                <div class="d-flex justify-content-center">
                                                    <input hidden type="file" name="pf_avata" id="pf_avatar" />
                                                    <input class="fr-btn btn-alt-avata d-none"
                                                        onclick="this.parentElement.querySelector('#pf_avatar').click()"
                                                        type="button" value="Avatar" />
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>:</td>
                                    <td><input disabled class="form-control" type="text" name="pf_mail"
                                            value="{{ auth()->user()->email }}" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mobile</td>
                                    <td>:</td>
                                    <td><input class="form-control" disabled type="text" name="pf_tel"
                                            value="{{ auth()->user()->phone == '' ? 'No information' : auth()->user()->phone }}" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td>:</td>
                                    <td class="flex-start">
                                        <div class="gender-not-edit">
                                            @switch(auth()->user()->gender)
                                                @case(0)
                                                    {{ 'Male' }}
                                                @break
                                                @case(1)
                                                    {{ 'Female' }}
                                                @break
                                                @case(2)
                                                    {{ 'others' }}
                                                @break
                                            @endswitch
                                        </div>
                                        <div class="gender-edit d-none">
                                            <div class="custom-radio">
                                                <input {{ auth()->user()->gender == 0 ? 'checked' : '' }} id="male"
                                                    type="radio" name="pf_sex" value="0" />
                                                <label for="male">Male</label>
                                            </div>
                                            <div class="custom-radio">
                                                <input {{ auth()->user()->gender == 1 ? 'checked' : '' }} id="fmale"
                                                    type="radio" name="pf_sex" value="1" />
                                                <label for="fmale">Female</label>
                                            </div>
                                            <div class="custom-radio">
                                                <input {{ auth()->user()->gender == 2 ? 'checked' : '' }} id="omale"
                                                    type="radio" name="pf_sex" value="2" />
                                                <label for="omale">Others</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Birdth Day</td>
                                    <td>:</td>
                                    <td colspan="2"><input class="form-control" disabled type="text" name="pf_birdth"
                                            value="{{ date('d/m/Y', strtotime(auth()->user()->birdth)) }}" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>:</td>
                                    <td colspan="2"><input class="form-control" disabled type="text" name="pf_add"
                                            value="{{ auth()->user()->address == '' ? 'No information' : auth()->user()->address }}" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="fr-btn d-none">SAVE</button>
                        </div>
                    </form>
                </div>
                <hr class="mb-5" />
                <div class="row acc-group">
                    <div class="acc-head mb-3">
                        <h3>Change Password</h3>
                        <p>Please perfect this form to change password.</p>
                    </div>
                    <form method="post" class="w-100" id="resetpass" data-action="{{ route('asbab.account.reset_password') }}">
                        <div class="form-group">
                            <span>Lastest Password:</span>
                            <input class="form-control" type="password" name="password" />
                            <div class="form-message"></div>
                        </div>
                        <div class="form-group">
                            <span>New Password:</span>
                            <input class="form-control" type="password" name="newpass" />
                            <div class="form-message"></div>
                        </div>
                        <div class="form-group">
                            <span>Re-enter Password:</span>
                            <input class="form-control" type="password" name="re_newpass" />
                            <div class="form-message"></div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" name="subChangePass" class="fr-btn">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <section class="slide-brand-area">
            <div class="container">
                <ul class="row owl-carousel owl-brand">
                    @foreach ($brands as $brand)
                        <li class="brand-logo"><a href="{{ $brand->link }}"><img src="{{ asset($brand->image_path) }}"
                                    alt="{{ $brand->name }}" /></a></li>
                    @endforeach
                </ul>
            </div>
        </section>
    </main>
@endsection
