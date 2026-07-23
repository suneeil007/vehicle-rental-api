<?php

namespace App\Modules\Booking\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Exceptions\NotFoundException;
use App\Exceptions\ConflictException;

use App\Modules\Booking\Models\Booking;
use App\Modules\Trip\Models\Trip;
use App\Modules\Booking\Repositories\Contracts\BookingRepositoryInterface;


class BookingService
{

    public function __construct(
        protected BookingRepositoryInterface $repository
    ) {}


    /**
     * List bookings
     */
    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }


    /**
     * Show booking
     */
    public function getById(int $id): Booking
    {

        $booking = $this->repository->getById($id);


        if(!$booking){

            throw new NotFoundException(
                'Booking not found.'
            );

        }


        return $booking;
    }



    /**
     * Create booking
     */
    public function create(array $data): Booking
    {

        return DB::transaction(function() use($data){


            return $this->repository->create([

                'slug'=>Str::uuid(),

                'customer_id'=>$data['customer_id'],

                'vehicle_id'=>$data['vehicle_id'],

                'rental_type'=>$data['rental_type'] 
                    ?? 'self_drive',

                'pickup_branch_id'=>$data['pickup_branch_id'],

                'drop_branch_id'=>$data['drop_branch_id'] ?? null,


                'pickup_at'=>$data['pickup_at'],

                'expected_return_at'=>$data['expected_return_at'],


                'quoted_amount'=>$data['quoted_amount'] ?? 0,

                'discount_amount'=>$data['discount_amount'] ?? 0,

                'final_amount'=>$data['final_amount']
                    ?? $data['quoted_amount'],


                'status'=>Booking::STATUS_PENDING,


                'customer_notes'=>$data['customer_notes'] ?? null,


            ]);

        });

    }



    /**
     * Approve booking
     */
    public function approve(
        Booking $booking,
        int $userId
    )
    {


        if(!$booking->canBeApproved()){

            throw new ConflictException(
                'Only pending booking can be approved.'
            );

        }


        return $this->repository->update(
            $booking,
            [

                'status'=>Booking::STATUS_APPROVED,

                'approved_by'=>$userId,

                'approved_at'=>now(),

            ]
        );

    }



    /**
     * Reject booking
     */
    public function reject(
        Booking $booking
    )
    {


        if(!$booking->isPending()){

            throw new ConflictException(
                'Only pending booking can be rejected.'
            );

        }


        return $this->repository->update(
            $booking,
            [

                'status'=>Booking::STATUS_REJECTED

            ]
        );

    }



    /**
     * Cancel booking
     */
    public function cancel(
        Booking $booking
    )
    {


        if(!$booking->canBeCancelled()){

            throw new ConflictException(
                'Booking cannot be cancelled.'
            );

        }



        return $this->repository->update(
            $booking,
            [

                'status'=>Booking::STATUS_CANCELLED

            ]
        );

    }




    /**
     * Convert booking into trip
     */
    public function createTrip(
        Booking $booking
    )
    {


        return DB::transaction(function() use($booking){



            if($booking->status !== Booking::STATUS_APPROVED){

                throw new ConflictException(
                    'Only approved booking can create trip.'
                );

            }



            $trip = Trip::create([


                'slug'=>Str::uuid(),


                'customer_id'=>$booking->customer_id,

                'vehicle_id'=>$booking->vehicle_id,


                'rental_type'=>$booking->rental_type,


                'pickup_branch_id'=>$booking->pickup_branch_id,

                'drop_branch_id'=>$booking->drop_branch_id,


                'pickup_at'=>$booking->pickup_at,

                'expected_return_at'=>$booking->expected_return_at,


                'pickup_odometer'=>0,

                'pickup_fuel'=>'full',


                'base_amount'=>$booking->final_amount,


                'total_amount'=>$booking->final_amount,


                'status'=>'scheduled'


            ]);





            $booking->update([

                'trip_id'=>$trip->id,

                'status'=>Booking::STATUS_TRIP_CREATED

            ]);




            return $booking->fresh([

                'trip'

            ]);



        });


    }



}