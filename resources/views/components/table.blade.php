<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                {{ $header }}
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            {{ $body }}
            </tbody>
        </table>
    </div>
    @if(isset($pagination))
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $pagination }}
        </div>
    @endif
</div>
