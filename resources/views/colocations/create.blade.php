<x-app-layout>
<div class="bg-gray-50 min-h-screen py-12 px-4">

  <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="bg-indigo-600 px-8 py-6 text-white">
      <h1 class="text-2xl font-bold">Create New Colocation</h1>
      <p class="text-indigo-100 text-sm opacity-90">Define your shared space and invite your roommates.</p>
    </div>

    <form action="{{ route('colocations.store') }}" method="POST" class="p-8 space-y-8">
      @csrf
      <section>
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">1. Basic Information</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Colocation Name</label>
            <input type="text" name="name" placeholder="e.g. The Sunny Loft, Baker Street Squad" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description & House Vibe</label>
            <textarea name="description" rows="3" placeholder="Describe the atmosphere, shared goals, or house rules..." 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"></textarea>
          </div>
        </div>
      </section>

      <div class="pt-6 flex items-center justify-between border-t border-gray-100">
        <button type="button" class="text-gray-500 font-medium hover:text-gray-700">Cancel</button>
        <button type="submit" class="bg-indigo-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all active:scale-95">
          Launch Colocation 🚀
        </button>
      </div>

    </form>
  </div>

</div>
</x-app-layout>