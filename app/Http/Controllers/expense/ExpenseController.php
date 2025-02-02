<?php

namespace App\Http\Controllers\expense;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        $query = Expense::with(['category', 'user'])
            ->whereBelongsTo($user);

        $filter = $request->query('filter', 'default');
        switch ($filter) {
            case 'LAST_WEEK':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;

            case 'LAST_MONTH':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;

            case 'LAST_3_MONTHS':
                $query->whereBetween('created_at', [now()->subMonths(3), now()]);
                break;

            case 'CUSTOM':
                $startDate = $request->query('start_date');
                $endDate = $request->query('end_date');

                if (!$startDate || !$endDate) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Start date and end date are required for custom filter.'
                    ], 400);
                }

                $query->whereBetween('created_at', [$startDate, $endDate]);
                break;

            default:

                break;
        }

        $expenses = $query->get();

        if ($expenses->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve expenses.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Expenses retrieved successfully.',
            'expenses' => $expenses
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255', 
            'category' => 'required|string|exists:categories,name',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'expense_date' => 'nullable|date' 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        $category = Category::where('name', $request->category)->first();

        try {
            $expense = Expense::create([
                'name' => $request->name,
                'category_id' => $category->id,
                'amount' => $request->amount,
                'description' => $request->description,
                'expense_date' => $request->expense_date ?? now(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense created successfully',
                'expense' => $expense
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create expense. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */

    public function show(string $id)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        $expense = Expense::with('category')->find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found.'
            ], 404);
        }

        if ($expense->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Expense retrieved successfully.',
            'expense' => $expense
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, string $id)
     {
    
         $validator = Validator::make($request->all(), [
             'name' => 'required|string|max:255',
             'category' => 'required|string|exists:categories,name',
             'amount' => 'required|numeric|min:0',
             'description' => 'required|string|max:255',
             'expense_date' => 'nullable|date'
         ]);
     
         if ($validator->fails()) {
             return response()->json([
                 'success' => false,
                 'message' => 'Validation errors',
                 'errors' => $validator->errors()
             ], 422);
         }
     
        
         $user = auth('api')->user();
         if (!$user) {
             return response()->json([
                 'success' => false,
                 'message' => 'User not authenticated.'
             ], 401);
         }
     
        
         $expense = Expense::with('category')->find($id);
         if (!$expense) {
             return response()->json([
                 'success' => false,
                 'message' => 'Expense not found.'
             ], 404);
         }
     
         if ($expense->user_id !== $user->id) {
             return response()->json([
                 'success' => false,
                 'message' => 'Forbidden'
             ], 403);
         }
     
         
         $category = Category::where('name', $request->category)->first();
     
         try {
            
             $updateData = [
                 'name' => $request->name,
                 'category_id' => $category->id,
                 'amount' => $request->amount,
                 'description' => $request->description,
                 'expense_date' => $request->expense_date ?? $expense->expense_date // Mantener valor existente si no se provee
             ];
     
             $expense->update($updateData);
     
            
             $expense->refresh()->load('category');
     
             return response()->json([
                 'success' => true,
                 'message' => 'Expense updated successfully',
                 'data' => $expense
             ], 200);
     
         } catch (Exception $e) {

             return response()->json([
                 'success' => false,
                 'message' => 'Failed to update expense. Please try again.',
                    'error' => $e->getMessage()
             ], 500);
         }
     }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found.'
            ], 404);
        }

        if ($expense->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden'
            ], 403);
        }

        try {
            $expense->delete();

            return response()->json([
                'success' => true,
                'message' => 'Expense deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete expense. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
