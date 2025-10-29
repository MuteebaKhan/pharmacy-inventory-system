<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Inventory Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* sidebar ka size adjust karo aur content ko push karo */
        
  aside {
    width: 16rem; /* 64 = 16rem */
  }
  .content-area {
    margin-left: 16rem; /* sidebar ke barabar */
  }

  /* responsive adjustment */
  @media (max-width: 1024px) {
    .content-area {
      margin-left: 0;
    }
  }

  
  
    </style>
    <style>
        .toast-enter { opacity: 0; transform: translateY(-10px); }
        .toast-enter-active { opacity: 1; transform: translateY(0); transition: all .2s ease; }
        .toast-exit { opacity: 1; }
        .toast-exit-active { opacity: 0; transition: opacity .2s ease; }
    </style>
</head>
<body class="antialiased font-sans">
    <div class="min-h-screen bg-gray-50">
        <!-- Toast -->
        <div id="toast" class="hidden fixed top-4 right-4 z-50 min-w-[260px] max-w-sm rounded-md shadow-lg px-4 py-3 border bg-white">
            <div class="flex items-start gap-3">
                <div id="toast-icon" class="mt-0.5"></div>
                <div class="flex-1">
                    <div id="toast-title" class="text-sm font-semibold text-gray-900"></div>
                    <div id="toast-message" class="mt-0.5 text-sm text-gray-700"></div>
                </div>
                <button type="button" class="ml-2 text-gray-400 hover:text-gray-600" onclick="hideToast()">✕</button>
            </div>
        </div>
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1  class="text-3xl font-bold text-gray-900">Pharmacy Inventory System</h1>
                        <p   class="mt-1 text-sm text-gray-600">Manage and monitor your inventory</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Last updated</div>
                        <div class="text-lg font-semibold text-gray-900" id="current-time"></div>
                    </div>
                </div>
            </div>
        </div>

        Sidebar
<aside class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg border-r border-gray-200 z-40">
  <div class="p-6 border-b border-gray-200">
    <h2 class="text-xl font-semibold text-emerald-700">Pharmacy Dashboard</h2>
  </div>

  <nav class="mt-4 px-4 space-y-2">
    <a href="/dashboard" class="block py-2 px-3 rounded-md hover:bg-emerald-50 hover:text-emerald-700 font-medium text-gray-700 transition">
      🏠 Dashboard
    </a>

    <button id="lowStockBtn" class="w-full text-left py-2 px-3 rounded-md hover:bg-red-50 hover:text-red-700 font-medium text-gray-700 transition">
      ⚠️ Low Stock Medicines
    </button>

     <button id="addMedicineBtn" class="w-full text-left py-2 px-3 rounded-md hover:bg-emerald-50 hover:text-emerald-700 font-medium text-gray-700 transition">
  ➕ Add Medicine
</button>

<button id="showGraphBtn" class="w-full text-left py-2 px-3 rounded-md hover:bg-blue-50 hover:text-blue-700 font-medium text-gray-700 transition">
📊 View Graph
</button>
  </nav>
</aside> 




        <!-- Stats Section -->
        <div class="content-area bg-gray-50 min-h-screen p-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Total Products -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_products'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="bg-white overflow-hidden shadow rounded-lg {{ $stats['low_stock_products'] > 0 ? 'ring-2 ring-red-300' : '' }}">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Low Stock Items</dt>
                                    <dd class="text-lg font-semibold text-red-600">{{ $stats['low_stock_products'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Value -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Value</dt>
                                    <dd class="text-lg font-semibold text-gray-900">${{ $stats['total_value'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Categories</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_categories'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Totals Row -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
                <!-- Total Purchases -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M12 3v18"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Purchases</dt>
                                    <dd class="text-lg font-semibold text-gray-900">${{ $stats['total_purchases_cost'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Sales -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18m-8-8h18"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Sales</dt>
                                    <dd class="text-lg font-semibold text-gray-900">${{ $stats['total_sales_amount'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Available Stock -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Available Stock</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_available_stock'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h2 id="inventoryTitle"  class="text-lg font-semibold text-gray-900">Product Inventory</h2>
                    <p id="inventorySubtitle"class="mt-1 text-sm text-gray-600">Complete list of all products in your inventory</p>
                </div>
                
                @if($products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody" class="bg-white divide-y divide-gray-200">
                                @foreach($products as $product)
                                    <tr class="{{ $product->quantity < 10 ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                    @if($product->description)
                                                        <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $product->category->name ?? 'Uncategorized' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="font-semibold">${{ number_format($product->price, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium {{ $product->quantity < 10 ? 'text-red-600' : 'text-gray-900' }}">
                                                    {{ $product->quantity }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($product->quantity < 10)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Low Stock
                                                </span>
                                            @elseif($product->quantity < 30)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Medium Stock
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    In Stock
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex gap-2 justify-end">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50"
                                                    data-edit
                                                    data-id="{{ $product->id }}"
                                                    data-name="{{ $product->name }}"
                                                    data-category_id="{{ $product->category_id }}"
                                                    data-price="{{ $product->price }}"
                                                    data-quantity="{{ $product->quantity }}"
                                                    data-description="{{ $product->description }}"
                                                    onclick="openEditModal(this)">
                                                    Edit
                                                </button>
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-md bg-red-600 text-white hover:bg-red-700"
                                                    onclick="openDeleteModal({ id: {{ $product->id }}, name: '{{ addslashes($product->name) }}' })">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No products</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding a new product to your inventory.</p>
                    </div>
                @endif
            </div>

            
               <!-- Chart -->
               <div id="graphSection" class="bg-white shadow overflow-hidden sm:rounded-lg mt-8 hidden">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Purchases, Sales, and Stock</h2>
        <p class="mt-1 text-sm text-gray-600">Overview of totals</p>
    </div>
    <div class="p-6">
        <canvas id="summaryChart" height="120"></canvas>
    </div>
</div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="hidden fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/40" onclick="closeEditModal()"></div>
        <div class="relative mx-auto mt-24 w-full max-w-2xl rounded-lg bg-white shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Edit Product</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeEditModal()">✕</button>
            </div>
            <div class="p-6">
                <form id="edit-form" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-id" />
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input id="edit-name" name="name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="edit-category" name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price</label>
                        <input id="edit-price" name="price" type="number" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input id="edit-quantity" name="quantity" type="number" min="0" required class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div class="md:col-span-5">
                        <label class="block text-xs text-gray-600">Description</label>
                        <textarea id="edit-description" name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="md:col-span-5 flex items-center justify-end gap-2 mt-2">
                        <button type="button" class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/40" onclick="closeDeleteModal()"></div>
        <div class="relative mx-auto mt-40 w-full max-w-md rounded-lg bg-white shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Delete Product</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeDeleteModal()">✕</button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-700">Are you sure you want to delete <span id="delete-name" class="font-semibold"></span>? This action cannot be undone.</p>
                <form id="delete-form" method="POST" class="mt-6 flex items-center justify-end gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md bg-red-600 text-white hover:bg-red-700">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Medicine Modal -->
    <div id="addMedicineModal" class="hidden fixed inset-0 backdrop-blur-[2px] bg-gray-800/20 flex justify-center items-center z-50">
    <div class="bg-white w-full max-w-3xl rounded-lg shadow-xl overflow-hidden">
    <div class="flex justify-between items-center p-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-black-700">➕ Add New Medicine</h3>
      <button id="closeAddMedicine" class="text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
    </div>

    <div class="px-6 py-6">
      <form method="POST" action="{{ route('dashboard.products.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
        @csrf
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input name="name" type="text" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Category</label>
          <select name="category_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">Select category</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Price</label>
          <input name="price" type="number" step="0.01" min="0" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Quantity</label>
          <input name="quantity" type="number" min="0" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
        </div>

        <div class="md:col-span-6">
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea name="description" rows="2"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
        </div>

        <div class="md:col-span-6 text-right">
          <button type="submit"
            class="inline-flex items-center px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            Add Product
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


    <!-- Update time and UI script -->
    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            });
            document.getElementById('current-time').textContent = timeString;
        }
        
        updateTime();
        setInterval(updateTime, 1000);

        // Toast helpers
        function showToast(type = 'success', title = 'Success', message = '') {
            const toast = document.getElementById('toast');
            const titleEl = document.getElementById('toast-title');
            const msgEl = document.getElementById('toast-message');
            const iconEl = document.getElementById('toast-icon');
            titleEl.textContent = title;
            msgEl.textContent = message;
            const color = type === 'error' ? 'red' : type === 'warning' ? 'yellow' : 'green';
            iconEl.innerHTML = `<svg class="h-5 w-5 text-${color}-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5h2v2H9v-2zm0-8h2v6H9V5z" clip-rule="evenodd"/></svg>`;
            toast.classList.remove('hidden');
            toast.classList.add('toast-enter-active');
            setTimeout(() => hideToast(), 2500);
        }
        function hideToast() {
            const toast = document.getElementById('toast');
            toast.classList.add('hidden');
        }

        // Trigger toast from session
        @if(session('toast_message'))
            showToast(`{{ session('toast_type', 'success') }}`, 'Success', `{{ session('toast_message') }}`);
        @endif

        // Modal helpers
        function openEditModal(btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const categoryId = btn.getAttribute('data-category_id');
            const price = btn.getAttribute('data-price');
            const quantity = btn.getAttribute('data-quantity');
            const description = btn.getAttribute('data-description') || '';

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-category').value = categoryId;
            document.getElementById('edit-price').value = price;
            document.getElementById('edit-quantity').value = quantity;
            document.getElementById('edit-description').value = description;

            const form = document.getElementById('edit-form');
            form.action = `/dashboard/products/${id}`;

            document.getElementById('edit-modal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        // Delete modal
        function openDeleteModal({ id, name }) {
            const form = document.getElementById('delete-form');
            form.action = `/dashboard/products/${id}`;
            document.getElementById('delete-name').textContent = name;
            document.getElementById('delete-modal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }

        // Chart.js
        document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('summaryChart').getContext('2d');
    const summaryChart = new Chart(ctx, {
        type: 'line', // 👈 line chart type
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], // months or any custom timeline
            datasets: [
                {
                    label: 'Purchases',
                    data: [12, 19, 15, 25, 20, 30], // replace with dynamic data later
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Sales',
                    data: [10, 14, 12, 22, 18, 28],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Stock',
                    data: [50, 48, 52, 47, 55, 53],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Inventory Trends Over Time'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
document.getElementById('showGraphBtn').addEventListener('click', () => {
    const graphSection = document.getElementById('graphSection');
    graphSection.classList.toggle('hidden'); // 👈 show/hide toggle
    
    // Optional: scroll to graph when shown
    if (!graphSection.classList.contains('hidden')) {
        graphSection.scrollIntoView({ behavior: 'smooth' });
    }
});

document.getElementById('lowStockBtn').addEventListener('click', async () => {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/low-stock');
        const data = await response.json();

       
        console.log(data); 

        if (data.success) {
            displayProducts(data.data);
        } else {
            alert("No data found or API error");
        }
    } catch (error) {
        console.error('Fetch Error:', error);
    }
});

function displayProducts(productList) {
  const tableBody = document.getElementById('productTableBody');
  tableBody.innerHTML = '';

  if (productList.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-6 text-gray-500 italic">
          🎉 All medicines are sufficiently stocked!
        </td>
      </tr>
    `;
    return;
  }

  productList.forEach(p => {
    const statusClass = p.quantity < 10 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
    const statusText = p.quantity < 10 ? 'Low Stock' : 'In Stock';

    const row = `
      <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 text-sm font-medium text-gray-900">${p.name}</td>
        <td class="px-6 py-4 text-sm text-gray-700">${p.category}</td>
        <td class="px-6 py-4 text-sm text-gray-700">$${p.price}</td>
        <td class="px-6 py-4 text-sm ${p.quantity < 10 ? 'text-red-600 font-semibold' : 'text-gray-800'}">${p.quantity}</td>
        <td class="px-6 py-4">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
            ${statusText}
          </span>
        </td>
      </tr>
    `;
    tableBody.innerHTML += row;
  });

}

        
    </script>

<script>
  const addMedicineBtn = document.getElementById('addMedicineBtn');
  const addMedicineModal = document.getElementById('addMedicineModal');
  const closeAddMedicine = document.getElementById('closeAddMedicine');

  addMedicineBtn.addEventListener('click', () => {
    addMedicineModal.classList.remove('hidden');
  });

  closeAddMedicine.addEventListener('click', () => {
    addMedicineModal.classList.add('hidden');
  });

  
  addMedicineModal.addEventListener('click', (e) => {
    if (e.target === addMedicineModal) {
      addMedicineModal.classList.add('hidden');
    }
  });

  document.getElementById('lowStockBtn').addEventListener('click', async () => {
  const response = await fetch('http://127.0.0.1:8000/api/low-stock');
  const data = await response.json();

  
  document.getElementById('inventoryTitle').textContent = 'Low Stock Medicines';
  document.getElementById('inventorySubtitle').textContent = 'Showing only medicines with less than 10 quantity.';

  if (data.success) {
    displayProducts(data.data);
  }
});

</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>

