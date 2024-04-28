<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Rental;
use App\Models\Sale;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;

class ManagerController extends Controller
{
    //
    public function index()
    {
        // Fetch users with role "owner" or "guest"
        $owners = User::whereIn('role', ['owner', 'guest'])->get();

        // Pass data to the view
        return view('manager.owner_manage', compact('owners'));
    }


    public function rental()
    {
        // Fetch rentals with their associated owner, category, and renter
        $rentals = DB::table('rentals')
            ->join('users as owners', 'rentals.owner_id', '=', 'owners.id')
            ->join('categories', 'rentals.category_id', '=', 'categories.id')
            ->select(
                'rentals.id as id',
                'owners.name as owner_name',
                'categories.name',
                'categories.rent_name',
                'rentals.rent_price',
                'rentals.start_date',
                'rentals.due_date',
                'rentals.paid_for_this_month'
            )
            ->get();
        $categories = Category::all();

        $groupedCategories = [];

        foreach ($categories as $category) {
            // Check if the category name already exists in the grouped data
            if (!isset($groupedCategories[$category->name])) {
                // If it doesn't exist, initialize an array for the category name
                $groupedCategories[$category->name] = [];
            }

            // Add the rent name to the array for the category name
            $groupedCategories[$category->name][] = $category->rent_name;
        }
        $owners = User::whereIn('role', ['owner'])->get();
        // Create array to hold event details for each rental
        $events = [];

        // Populate events array with event details for each rental
        foreach ($rentals as $rental) {
            $events[] = [
                'title' => 'Rent for ' . $rental->owner_name,
                'start' => $rental->start_date,
                'end' => $rental->due_date,
            ];
        }
//        dd($events);
        // Pass both rentals and events data to the view
        return view('manager.rental_manage', compact('rentals', 'events', 'owners', 'groupedCategories'));
    }






    public function store(Request $request)
    {
        // Check if the request is for a guest rental
        if ($request->owner_guest_toggle == 'guest') {
            // Create a new user for the guest
            // Retrieve the latest guest's email address
            $latestGuestEmail = User::where('email', 'like', 'guest+%')->latest()->value('email');

            // Extract the number from the email address and increment it
            $guestNumber = 0;
            if ($latestGuestEmail) {
                preg_match('/guest\+(\d+)@example.com/', $latestGuestEmail, $matches);
                $guestNumber = intval($matches[1]) + 1;
            }

            // Construct the new guest's email address
            $newGuestEmail = 'guest+' . $guestNumber . '@example.com';

            // Create a new guest user
            $guest = new User();
            $guest->name = $request->guest_name; // Assuming you have a 'name' field in your user table
            $guest->email = $newGuestEmail;
            $guest->password = bcrypt('guestpassword'); // You can set a default password for guests
            $guest->role = 'guest';
            $guest->active = 1;
            $guest->save();

            // Check if the category ID and rent name match
            $category = Category::where('name', $request->category_id)->where('rent_name', $request->rent_name)->first();
            if (!$category) {
                // Return error response if the category ID and rent name don't match
                return redirect()->back()->with('error', 'Invalid category or rent name');
            }

            // Create a new rental instance for the guest
            $rental = new Rental();
            $rental->owner_id = $guest->id; // Assign the guest's ID to the rental's user_id field
            $rental->category_id = $category->id;
            $rental->start_date = $request->start_date;
            $rental->due_date = $request->end_date;
            $rental->rent_price = $request->rent_price;
            $rental->save();

        } else {
            $category = Category::where('name', $request->category_name)->where('rent_name', $request->rent_name)->first();
            if (!$category) {
                // Return error response if the category ID and rent name don't match
                return redirect()->back()->with('error', 'Invalid category or rent name');
            }

            // Create a new rental instance for the owner
            $rental = new Rental();
            $rental->owner_id = $request->owner_id;
            $rental->category_id = $category->id;
            $rental->start_date = $request->start_date;
            $rental->due_date = $request->end_date;
            $rental->rent_price = $request->rent_price;
            $rental->save();
        }
        // Return success response
//        return redirect()->back()->with('success', 'Rental added successfully');
    }


    public function toggleStatus(Request $request, User $user)
    {
        $newStatus = $request->input('active', false); // Default to false if not provided
        $user->active = $newStatus;
        $user->save();

        return response()->json(['message' => 'Owner status updated successfully.']);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        // Find the owner by ID
        $owner = User::findOrFail($id);

        // Update the owner's name and email
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Owner details updated successfully');
    }

    public function statistical()
    {
        // Get the count of rentals that are paid
        $paidCount = Rental::where('paid_for_this_month', true)->count();

        // Get the total rent price of all paid rentals
        $totalIncome = Rental::where('paid_for_this_month', true)->sum('rent_price');

        // Get the number of unique owners who have paid
        $uniqueOwnersPaidCount = Rental::where('paid_for_this_month', true)->distinct('owner_id')->count('owner_id');

        // Get the count of rentals that are unpaid
        $unpaidCount = Rental::where('paid_for_this_month', false)->count();

        // Get the total potential income from unpaid rentals
        $potentialIncome = Rental::where('paid_for_this_month', false)->sum('rent_price');

        $monthlyIncomeData = Rental::where('paid_for_this_month', true)
            ->whereYear('start_date', Carbon::now()->year)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->start_date)->format('F');
            })
            ->map(function($month) {
                return $month->sum('rent_price');
            });

        // Return the statistical data to the view
        return view('manager.statistical_manage', [
            'paidCount' => $paidCount,
            'totalIncome' => $totalIncome,
            'uniqueOwnersPaidCount' => $uniqueOwnersPaidCount,
            'unpaidCount' => $unpaidCount,
            'potentialIncome' => $potentialIncome,
            'monthlyIncomeData' => $monthlyIncomeData
        ]);
    }

    function category()
    {
        $categories = Category::all();
        return view('manager.rental_category', compact('categories'));
    }
    function categoryStore(Request $request)
    {
        $validatedData = $request->validate([
            'rental_item_name' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
        ]);

        // Create a new category instance
        $category = new Category();
        $category->rent_name = $validatedData['rental_item_name'];
        $category->name = $validatedData['category_name'];
        $category->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Category added successfully.');
    }

    public function delete($id): JsonResponse
    {
        $rental = Rental::findOrFail($id);
        $rental->delete();

        return response()->json(['message' => 'Rental successfully deleted'], 200);
    }

    public function generatePDF()
    {
        // Get the current month and year
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Fetch all rentals for the current month with owner names and rental names
        $rentals = Rental::select('rentals.*', 'users.name as owner_name', 'categories.rent_name as rental_name')
            ->leftJoin('users', 'rentals.owner_id', '=', 'users.id')
            ->leftJoin('categories', 'rentals.category_id', '=', 'categories.id')
            ->whereMonth('rentals.created_at', $currentMonth)
            ->whereYear('rentals.created_at', $currentYear)
            ->get();

        // Separate paid and unpaid rentals
        $paidRentals = $rentals->where('paid_for_this_month', true);
        $unpaidRentals = $rentals->where('paid_for_this_month', false);

        // Calculate total income from paid rentals
        $totalIncomePaid = $paidRentals->sum('rent_price');

        // Calculate total potential income from unpaid rentals
        $totalPotentialIncomeUnpaid = $unpaidRentals->sum('rent_price');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rental_report', compact('paidRentals', 'unpaidRentals', 'totalIncomePaid', 'totalPotentialIncomeUnpaid'));

        return $pdf->download('rental_report_-' . now()->format('F_Y') . '.pdf');
    }




    public function ownerStat($userId)
    {
        // Find users with the role "owner"
        $owners = User::where('role', 'owner')->get();
        $selectedOwner = null;

        if (!$userId) {
            // If $userId is null, default to the first owner
            $selectedOwner = $owners->first();
        } else {
            // If $userId is provided, find the owner with that ID
            $selectedOwner = $owners->find($userId);
        }

        // Check if a valid owner is selected
        if ($selectedOwner) {
            // Get daily income
            $dailyIncome = Sale::where('user_id', $selectedOwner->id)
                ->whereDate('created_at', today())
                ->sum('total_amount');

            // Get monthly income
            $monthlyIncome = Sale::where('user_id', $selectedOwner->id)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount');

            // Get yearly income
            $yearlyIncome = Sale::where('user_id', $selectedOwner->id)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount');

            // Get weekly income for the past 4 weeks
            $weeklyIncomePast4Weeks = Sale::where('user_id', $selectedOwner->id)
                ->whereDate('created_at', '>=', now()->subWeeks(4))
                ->orderBy('created_at')
                ->get()
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->weekOfYear;
                })
                ->map(function ($weeklyIncome) {
                    return $weeklyIncome->sum('total_amount');
                });

            // Check if the current week is greater than 4
            $currentWeek = Carbon::now()->weekOfYear;

            // Get weekly income for the past 4 weeks
            $weeklyIncomePast4Weeks = Sale::where('user_id', $selectedOwner->id)
                ->whereDate('created_at', '>=', now()->subWeeks(4))
                ->orderBy('created_at')
                ->get()
                ->groupBy(function ($date) use ($currentWeek) {
                    return Carbon::parse($date->created_at)->weekOfYear - ($currentWeek - 4);
                })
                ->map(function ($weeklyIncome) {
                    return $weeklyIncome->sum('total_amount');
                });

            // Fill in missing weeks with zero values and format keys as "Week X"
            $WeeklyIncomePast4Weeks = [];
            for ($weekNumber = 1; $weekNumber <= 4; $weekNumber++) {
                $weekLabel = "Week $weekNumber";
                if (isset($weeklyIncomePast4Weeks[$weekNumber])) {
                    $WeeklyIncomePast4Weeks[$weekLabel] = $weeklyIncomePast4Weeks[$weekNumber];
                } else {
                    $WeeklyIncomePast4Weeks[$weekLabel] = 0;
                }
            }

            // Get monthly income for the past 3 months (quarterly)
            $monthlyIncomePast3Months = Sale::where('user_id', $selectedOwner->id)
                ->whereDate('created_at', '>=', now()->subMonths(3))
                ->orderBy('created_at')
                ->get()
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format('F');
                })
                ->map(function ($monthlyIncome) {
                    return $monthlyIncome->sum('total_amount');
                });

            // Get the current year
            $currentYear = date('Y');
            $yearlyIncomeData = [];

            for ($year = $currentYear - 2; $year <= $currentYear; $year++) {
                // Get yearly income for the current year
                $yearlyIncome = Sale::where('user_id', $selectedOwner->id)
                    ->whereYear('created_at', $year)
                    ->sum('total_amount');

                // Store the yearly income for the current year in the array
                $yearlyIncomeData[$year] = $yearlyIncome;
            }

            // Get popular food items
            $popularFoods = DB::table('sales')
                ->join('inventories', 'sales.product_id', '=', 'inventories.id')
                ->where('sales.user_id', $selectedOwner->id)
                ->select('inventories.name', DB::raw('SUM(sales.quantity_sold) as total_quantity'))
                ->groupBy('inventories.name')
                ->orderByDesc('total_quantity')
                ->limit(5) // Limit to top 5 popular food items
                ->get();

            return view('manager.ownerStat_manage', compact(
                'dailyIncome',
                'monthlyIncome',
                'yearlyIncome',
                'WeeklyIncomePast4Weeks',
                'monthlyIncomePast3Months',
                'yearlyIncomeData',
                'popularFoods',
                'owners',
                'selectedOwner'
            ));
        } else {
            // If no valid owner is selected, return an error message or redirect as needed
            return redirect()->back()->with('error', 'Invalid owner selected.');
        }
    }

//    public function ownerStats($useId)
//    {
//        $owners = User::where('role','owner')->get();
//        $dailyIncome = Sale::where('user_id', $useId)
//            ->whereDate('created_at', today())
//            ->sum('total_amount');
//
//        // Get monthly income
//        $monthlyIncome = Sale::where('user_id', $useId)
//            ->whereYear('created_at', now()->year)
//            ->whereMonth('created_at', now()->month)
//            ->sum('total_amount');
//
//        // Get yearly income
//        $yearlyIncome = Sale::where('user_id', $useId)
//            ->whereYear('created_at', now()->year)
//            ->sum('total_amount');
//
//        // Get weekly income for the past 4 weeks
//        $weeklyIncomePast4Weeks = Sale::where('user_id', $useId)
//            ->whereDate('created_at', '>=', now()->subWeeks(4))
//            ->orderBy('created_at')
//            ->get()
//            ->groupBy(function ($date) {
//                return Carbon::parse($date->created_at)->weekOfYear;
//            })
//            ->map(function ($weeklyIncome) {
//                return $weeklyIncome->sum('total_amount');
//            });
//
//        // Check if the current week is greater than 4
//        $currentWeek = Carbon::now()->weekOfYear;
//
//// Get weekly income for the past 4 weeks
//        $weeklyIncomePast4Weeks = Sale::where('user_id', $useId)
//            ->whereDate('created_at', '>=', now()->subWeeks(4))
//            ->orderBy('created_at')
//            ->get()
//            ->groupBy(function($date) use ($currentWeek) {
//                return Carbon::parse($date->created_at)->weekOfYear - ($currentWeek - 4);
//            })
//            ->map(function($weeklyIncome) {
//                return $weeklyIncome->sum('total_amount');
//            });
//
//// Fill in missing weeks with zero values and format keys as "Week X"
//        $WeeklyIncomePast4Weeks = [];
//        for ($weekNumber = 1; $weekNumber <= 4; $weekNumber++) {
//            $weekLabel = "Week $weekNumber";
//            if (isset($weeklyIncomePast4Weeks[$weekNumber])) {
//                $WeeklyIncomePast4Weeks[$weekLabel] = $weeklyIncomePast4Weeks[$weekNumber];
//            } else {
//                $WeeklyIncomePast4Weeks[$weekLabel] = 0;
//            }
//        }
//
//        // Get monthly income for the past 3 months (quarterly)
//        $monthlyIncomePast3Months = Sale::where('user_id', $useId)
//            ->whereDate('created_at', '>=', now()->subMonths(3))
//            ->orderBy('created_at')
//            ->get()
//            ->groupBy(function ($date) {
//                return Carbon::parse($date->created_at)->format('F');
//            })
//            ->map(function ($monthlyIncome) {
//                return $monthlyIncome->sum('total_amount');
//            });
//
//        // Get the current year
//        $currentYear = date('Y');
//        $yearlyIncomeData = [];
//
//        for ($year = $currentYear - 2; $year <= $currentYear; $year++) {
//            // Get yearly income for the current year
//            $yearlyIncome = Sale::where('user_id', $useId)
//                ->whereYear('created_at', $year)
//                ->sum('total_amount');
//
//            // Store the yearly income for the current year in the array
//            $yearlyIncomeData[$year] = $yearlyIncome;
//        }
//        // Get popular food items
//        $popularFoods = DB::table('sales')
//            ->join('inventories', 'sales.product_id', '=', 'inventories.id')
//            ->where('sales.user_id', $useId)
//            ->select('inventories.name', DB::raw('SUM(sales.quantity_sold) as total_quantity'))
//            ->groupBy('inventories.name')
//            ->orderByDesc('total_quantity')
//            ->limit(5) // Limit to top 5 popular food items
//            ->get();
//        return view('manager.ownerStat_manage', compact(
//            'dailyIncome',
//            'monthlyIncome',
//            'yearlyIncome',
//            'WeeklyIncomePast4Weeks',
//            'monthlyIncomePast3Months',
//            'yearlyIncomeData',
//            'popularFoods',
//            'owners'
//        ));
//    }


}
