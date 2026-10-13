<section class="card">
    <header class="card-header">
        <div>
            <h2 class="card-title">{{ __('Profile Information') }}</h2>
            <p class="card-subtitle">{{ __("Update your account's profile information and email address.") }}</p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card-body space-y-6">
        @csrf
        @method('patch')

        {{-- Photo preview --}}
        <div class="flex items-center gap-4">
            <span class="avatar avatar--xl">
                @if (auth()->user()->photo_path)
                <img src="{{ asset('storage/' . auth()->user()->photo_path) }}" alt="">
                @else
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </span>
            <div>
                <label class="btn btn-outline btn-sm" for="photo">Change photo</label>
                <input type="file" name="photo" id="photo" accept="image/*" class="hidden"
                    onchange="const f=this.files[0]; if(!f)return; const fR=new FileReader(); fR.onload=e=>{const img=new Image(); img.onload=()=>{const c=document.createElement('canvas'); let w=img.width,h=img.height; const m=600; if(w>m||h>m){ if(w>=h){h=Math.round(h*m/w);w=m;} else {w=Math.round(w*m/h);h=m;} } c.width=w;c.height=h; c.getContext('2d').drawImage(img,0,0,w,h); document.getElementById('photo-preview').src=c.toDataURL('image/jpeg',.82); }; img.src=e.target.result;}; fR.readAsDataURL(f);"
                    onload="this.closest('div').querySelector('img')?.setAttribute('src', this.dataset.initial || '')">
                @if (auth()->user()->photo_path)
                <img id="photo-preview" src="{{ asset('storage/' . auth()->user()->photo_path) }}" width="0" height="0"
                    class="hidden rounded">
                @else
                <img id="photo-preview" class="hidden rounded" width="0" height="0">
                @endif
                <p class="field-hint">JPG or PNG. Resized to 600px on upload.</p>
            </div>
        </div>

        <div>
            <x-floating-input name="name" label="Name" :value="auth()->user()->name" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-floating-input name="email" label="Email" type="email" :value="auth()->user()->email" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
            <div class="alert alert-warning mt-4">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <div>
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="text-action">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </div>
            </div>

            @if (session('status') === 'verification-link-sent')
            <p class="field-error" style="color: var(--teal-600); margin-top: 8px;">
                {{ __('A new verification link has been sent to your email address.') }}
            </p>
            @endif
            @endif
        </div>

        <div class="flex items-center justify-between border-t pt-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-muted">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
