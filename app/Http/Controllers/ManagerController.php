<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;

class ManagerController extends Controller
{
    //
    public function index()
    {
        $owners = User::where('role', 'owner')->get();
        // You can pass data to the view if necessary
        return view('manager.owner_manage', compact('owners'));
    }

    public function rental()
    {
        // Fetch owners and categories
        $owners = User::where('role', 'owner')->get();
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
        // Fetch rentals with their associated owner and category
        $rentals = Rental::with(['owner', 'category'])->get();
        $startDates = Rental::pluck('start_date')->toArray();
        $dueDates = Rental::pluck('due_date')->toArray();

        $bookedDates = array_merge($startDates, $dueDates);
        // Pass data to the view
        return view('manager.rental_manage', compact('rentals', 'owners', 'categories', 'bookedDates', 'groupedCategories'));
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
        // Render the Blade view to HTML
        $data = [
            'title' => 'Sample PDF Report',
            'content' => 'This is a sample PDF report generated using Laravel and spatie/laravel-pdf.'
        ];
        $filename = 'report_' . time() . '.pdf';
        $directory = storage_path('app/public/pdf/');
        Pdf::view('pdf.report', ['data' => $data])
            ->save('C:/htdocs/igp-dashboard/storage/pdf/invoice.pdf');


        return 'PDF report saved successfully!';
    }

    public function ownerStat()
    {
        return view('manager.ownerStat_manage');
    }


}
