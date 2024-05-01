<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    //
    public function casher()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Fetch all products belonging to the user and group them by category
        $productsByCategory = Inventory::where('user_id', $userId)
            ->orderBy('category')
            ->get()
            ->groupBy('category');

        // Pass the grouped products to the view
        return view('owner.casher_dashboard', ['productsByCategory' => $productsByCategory]);
    }
    public function saveSalesData(Request $request)
    {
        $salesData = $request->input('salesData');

        foreach ($salesData as $saleItem) {
            // Find the product by name
            $product = Inventory::where('name', $saleItem['productName'])->first();

            // If the product exists, proceed to save the sale
            if ($product) {
                $sale = new Sale();
                $sale->user_id = auth()->id(); // Assuming you're using authentication
                $sale->product_id = $product->id; // Get the product ID from the inventory
                $sale->quantity_sold = $saleItem['quantitySold'];
                $sale->total_amount = $saleItem['totalAmount'];
                $sale->save();

                // Update current quantity of the product
                $product->current_quantity -= $saleItem['quantitySold'];
                $product->save();
            }
        }
        return response()->json(['message' => 'Sales data saved successfully']);
    }


    public function inventory()
    {
        // Get the current authenticated user's ID
        $userId = auth()->id();

        // Fetch all inventory items belonging to the logged-in user
        $inventories = Inventory::where('user_id', $userId)->get();

        // Return the view with the inventory data
        return view('owner.inventory_dashboard', compact('inventories'));
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'initial_quantity' => 'required|integer|min:1'
        ]);

        // Create the new inventory item using the validated data
        $inventory = new Inventory();
        $inventory->user_id = auth()->id();  // Assuming the user needs to be authenticated to add inventory
        $inventory->name = $validatedData['name'];
        $inventory->description = $validatedData['description'];
        $inventory->category = $validatedData['category'];
        $inventory->price = $validatedData['price'];
        $inventory->initial_quantity = $validatedData['initial_quantity'];
        $inventory->current_quantity = $validatedData['initial_quantity']; // Initialize current quantity to the initial quantity
        $inventory->save();

        // Redirect back to the inventory list with a success message
        return redirect()->back()->with('success', 'Inventory item added successfully!');
    }

    public function restock(Request $request, $itemId)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'currentQuantity' => 'required|integer|min:0',
            'initialQuantity' => 'nullable|integer|min:0',
        ]);

        // Find the inventory item by ID belonging to the authenticated user
        $inventory = Inventory::findOrFail($itemId);

        // Check if the current quantity is different from the existing current quantity
        if ($validatedData['currentQuantity'] !== $inventory->current_quantity) {
            $inventory->current_quantity = $validatedData['currentQuantity'];
        }

        // Check if the initial quantity has been modified
        if (isset($validatedData['initialQuantity']) && $validatedData['initialQuantity'] !== $inventory->initial_quantity) {
            $inventory->initial_quantity = $validatedData['initialQuantity'];
        }

        // Save the changes
        $inventory->save();
        // Redirect back with success message
        return redirect()->back()->with('success', 'Inventory restocked successfully.');
    }

    public function removeStock($id)
    {
        // Find the inventory item by ID belonging to the authenticated user
        $inventory = Inventory::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        $inventory->current_quantity = 0;
        $inventory->save();
        return redirect()->back()->with('success', 'Stock removed successfully.');
    }

    public function incomeView(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d')); // Default to today if no date is provided
        $endDate = $request->input('endDate', $date); // Allows for fetching ranges, defaults to the same as start date

        $sales = Sale::whereBetween('created_at', [$date . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('product') // Assuming you have a relationship setup
            ->get();

        $totalIncome = $sales->sum('total_amount');

        return view('owner.income_dashboard', compact('sales', 'totalIncome', 'date', 'endDate'));
    }

    public function generatePDF(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        // Ensure the date format is correct and adjust for start and end of day
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Fetch sales data within the specified date range for the authenticated user
        $sales = Sale::where('user_id', auth()->id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Calculate total amount for the given date range
        $totalAmount = $sales->sum('total_amount');

        // Load the view with sales data, start and end date, and total amount
        $pdf = PDF::loadView('pdf.report', compact('sales', 'startDate', 'endDate', 'totalAmount'));
        // Download the PDF with a meaningful filename
        return $pdf->download('sales-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
    }

    public function ownerStat()
    {
        $userId = auth()->id(); // Assuming you're using authentication

        // Get daily income
        $dailyIncome = Sale::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->sum('total_amount');

        // Get monthly income
        $monthlyIncome = Sale::where('user_id', $userId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        // Get yearly income
        $yearlyIncome = Sale::where('user_id', $userId)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Get weekly income for the past 4 weeks
        $weeklyIncomePast4Weeks = Sale::where('user_id', $userId)
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
        $weeklyIncomePast4Weeks = Sale::where('user_id', $userId)
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

        // Get monthly income for the current month and past 4 months
        $monthlyIncomePast5Months = [];
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Calculate the starting month for the loop
        $startMonth = ($currentMonth - 3 <= 0) ? 12 + ($currentMonth - 3) : $currentMonth - 3;

        for ($i = 0; $i < 4; $i++) {
            $month = ($startMonth + $i <= 12) ? $startMonth + $i : $startMonth + $i - 12;
            $year = ($startMonth + $i <= 12) ? $currentYear : $currentYear - 1;

            $monthlyIncomePast5Months[Carbon::createFromDate($year, $month, 1)->format('F')] = Sale::where('user_id', $userId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('total_amount');
        }

        // Reorder the array so that the current month is at the end
        if (isset($monthlyIncomePast5Months[now()->format('F')])) {
            $currentMonthIncome = $monthlyIncomePast5Months[now()->format('F')];
            unset($monthlyIncomePast5Months[now()->format('F')]);
            $monthlyIncomePast5Months[now()->format('F')] = $currentMonthIncome;
        }


        // Get the current year
        $currentYear = date('Y');
        $yearlyIncomeData = [];

        for ($year = $currentYear - 2; $year <= $currentYear; $year++) {
            // Get yearly income for the current year
            $yearlyIncome = Sale::where('user_id', $userId)
                ->whereYear('created_at', $year)
                ->sum('total_amount');

            // Store the yearly income for the current year in the array
            $yearlyIncomeData[$year] = $yearlyIncome;
        }
        // Get popular food items
        $popularFoods = DB::table('sales')
            ->join('inventories', 'sales.product_id', '=', 'inventories.id')
            ->where('sales.user_id', $userId)
            ->select('inventories.name', DB::raw('SUM(sales.quantity_sold) as total_quantity'))
            ->groupBy('inventories.name')
            ->orderByDesc('total_quantity')
            ->limit(5) // Limit to top 5 popular food items
            ->get();
        return view('owner.statistical_dashboard', compact(
            'dailyIncome',
            'monthlyIncome',
            'yearlyIncome',
            'WeeklyIncomePast4Weeks',
            'monthlyIncomePast5Months',
            'yearlyIncomeData',
            'popularFoods'
        ));
    }

}
