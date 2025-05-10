@if ($requires_identity_validation)
<div class="card mt-3 bg-light">
    <div class="card-body">
        @if (empty($identity))
        <div class="row">
            <div class="col-md-8">
                <h5 class="mb-3 fw-bold">Validasi Identitas</h5>
                <form wire:submit="validateIdentity">
                    <div class="mb-3">
                        <label for="dob" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" wire:model="dob" id="dob">
                        @error('dob')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Unggah Dokumen (Kartu Pelajar)</label>
                        <input type="file" class="form-control" wire:model="document" id="document">
                        @error('document')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="d-flex gap-2 align-items-center justify-content-between">
                        <button type="submit" class="btn btn-primary">Kirim</button>

                        <div wire:target='document' wire:loading.class.remove='d-none' class="d-none  spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                @if ($document)

                    <div class="card text-start" style="width:100%; height: 250px; object-fit: cover;">
                        <img class="card-img placeholder" src="{{ $document->temporaryUrl() }}" style="width:100%; height: 250px; object-fit: cover;" alt="identity" />
                    </div>

                @else

                    <div class="card placeholder" style="width:100%; height: 250px; object-fit: cover;">
                        <div class="card-body placeholder"></div>
                    </div>

                @endif
            </div>
        </div>
        @else
            <div class="row justify-content-between">
                <div class="col-md-5">
                    <h5 class="mb-3 fw-bold">Profil Pelanggan</h5>
                    <div class="pb-3">
                        <p class="small mb-0">Nama Lengkap</p>
                        <p class="h6">{{ $user->name }}</p>
                        <p class="small mb-0">Tanggal Lahir</p>
                        <p class="h6">{{ Carbon\Carbon::parse($user->identity->dob)->format('d-m-Y') }}
                        </p>
                        <p class="small mb-0">Email</p>
                        <p class="h6">{{ $user->email }}</p>
                        <p class="small mb-0">Telepon</p>
                        <p class="h6">{{ $user->phone }}</p>
                        <p class="small mb-0">Mendaftar Pada</p>
                        <p class="h6">
                            {{ Carbon\Carbon::parse($user->created_at)->format('d-m-Y h:i:s') }}</p>

                        <a class="icon-link" href="{{ route('profile.guest') }}">
                            Edit Profile ->
                        </a>
                    </div>
                </div>
                <div class="col-md-7 text-md-end">
                    <h5 class="mb-3 fw-bold">Identitas Pelajar</h5>
                    <a href="{{ Storage::url($identity->document) }}" data-fancybox>
                        <img src="{{ Storage::url($identity->document) }}"
                            class="img-fluid rounded" style="object-fit:cover; height: 250px;"
                            alt="ducument identity user" />
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endif