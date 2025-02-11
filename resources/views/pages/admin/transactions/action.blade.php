 <?php

 use Jantinnerezo\LivewireAlert\LivewireAlert;
 use function Livewire\Volt\{state, uses};

 state([
     'user' => fn() => $this->booking->user,
     'requires_identity_validation' => fn() => $this->booking->bookingTimes->contains(fn($item) => $item->type === 'STUDENT'),
     'booking',
 ]);
 ?>

 <div>
     @push('scripts')
         <script>
             document.getElementById('printInvoiceBtn').addEventListener('click', function() {
                 window.print(); // Fungsi bawaan browser untuk mencetak halaman
             });
         </script>
     @endpush
     @volt
         <div>
             <section class="py-3 py-md-5">
                 <div class="row mb-4">
                     <div class="col-6">
                         <button class="btn btn-primary btn-lg text-uppercase">
                             {{ __('booking.' . $booking->status) }}
                         </button>

                     </div>
                     <div class="col-6 text-end">
                         <button type="button" class="btn btn-dark btn-lg d-print-none" id="printInvoiceBtn">Download
                             Invoice</button>

                     </div>
                 </div>

                 <div class="row gy-3 mb-3">
                     <div class="col-6">
                         <h4 class="text-uppercase text-primary m-0">Invoice</h4>
                     </div>
                     <div class="col-6">
                         <h4 class="text-uppercase text-primary text-end m-0">{{ $booking->invoice }}</h4>
                     </div>
                 </div>
                 <div class="row mb-3">
                     <small class="h5 fw-bold">Penyewaan</small>
                     <div class="col-12 col-sm-6 col-md-8">
                         <address>
                             <div>{{ $user->name }}</div>
                             <div>{{ $user->email }}</div>
                             <div>{{ $user->phone }}</div>
                         </address>
                     </div>
                     <div class="col-12 col-sm-6 col-md-4 text-end">
                         <address>
                             <div>{{ $booking->created_at->format('d m Y h:i:s') }}</div>
                             <div>
                                 Metode Pembayaran :
                                 {{ __('status.' . $booking->payment_method) }}
                             </div>
                             <div>
                                 No. Telp Alternatif :
                                 {{ $booking->alternative_phone ?? '-' }}
                             </div>

                         </address>
                     </div>
                 </div>
             </section>

             <section class="row mb-3">
                 <div class="col-12">
                     <div class="table-responsive">
                         <table class="table table-striped text-center">
                             <thead>
                                 <tr>
                                     <th scope="col" class="text-uppercase">Lapangan</th>
                                     <th scope="col" class="text-uppercase">Hari</th>
                                     <th scope="col" class="text-uppercase">Jam</th>
                                     <th scope="col" class="text-uppercase text-end">Type</th>
                                     <th scope="col" class="text-uppercase text-end">Harga</th>
                                 </tr>
                             </thead>
                             <tbody class="table-group-divider">
                                 @foreach ($booking->bookingTimes as $time)
                                     <tr>
                                         <th>{{ $time->field->field_name }}</th>
                                         <th>{{ Carbon\Carbon::parse($time->booking_date)->format('d-m-Y') }}
                                         </th>
                                         <td>{{ $time->start_time . ' - ' . $time->end_time }}</td>
                                         <td class="text-end">
                                             {{ __('type.' . $time->type) }}
                                         </td>
                                         <td class="text-end">
                                             {{ formatRupiah($time->price) }}
                                         </td>
                                     </tr>
                                 @endforeach

                                 <tr>
                                     <th scope="row" colspan="4" class="text-uppercase text-end">
                                         Total
                                     </th>
                                     <td class="text-end">
                                         {{ formatRupiah($booking->bookingTimes->sum('price')) }}
                                     </td>
                                 </tr>
                             </tbody>
                         </table>
                     </div>
                 </div>
             </section>
         </div>
     @endvolt
 </div>
