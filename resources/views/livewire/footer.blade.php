<footer class="bg-gray-800 text-white mt-auto">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Company Info -->
            <div>
                <h3 class="text-lg font-semibold mb-4">KickPredict</h3>
                <p class="text-gray-300 mb-4">
                    AI-powered soccer match predictions based on advanced machine learning algorithms.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white">Home</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white">About Us</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white">How It Works</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white">Contact</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Legal</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('privacy-policy') }}" class="text-gray-300 hover:text-white">Privacy Policy</a></li>
                </ul>
                <div class="mt-4 text-xs text-gray-400">
                    <p class="mb-2"><strong>Disclaimer:</strong></p>
                    <p>Our predictions are AI-generated forecasts based on statistical analysis and machine learning algorithms. They are for informational purposes only and should not be considered as betting advice or guarantees of match outcomes.</p>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
            <div class="text-gray-300 text-sm">
                © {{ date('Y') }} KickPredict. All rights reserved.
            </div>
            <div class="text-gray-300 text-sm mt-4 md:mt-0">
                <span class="mr-4">Responsible Gaming</span>
            </div>
        </div>
    </div>
</footer> 