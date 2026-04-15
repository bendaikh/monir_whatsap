@extends('layouts.customer')

@section('title', 'AI Campaign Creator')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="material-icons text-purple-600 text-4xl">auto_awesome</span>
                    AI Campaign Creator
                </h1>
                <p class="text-gray-600 mt-1">Create and publish ad campaigns with AI assistance</p>
            </div>
            <a href="{{ route('app.ad-campaigns') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition inline-flex items-center gap-2">
                <span class="material-icons text-sm">arrow_back</span>
                Back to Campaigns
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <span class="material-icons">error</span>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($facebookAccounts->isEmpty() && $tiktokAccounts->isEmpty())
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-lg mb-6">
        <div class="flex items-start gap-3">
            <span class="material-icons">warning</span>
            <div>
                <p class="font-semibold mb-2">No Ad Accounts Connected</p>
                <p class="text-sm mb-3">You need to connect at least one ad account before creating campaigns.</p>
                <div class="flex gap-3">
                    <a href="{{ route('app.facebook-ads') }}" class="text-sm px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                        Connect Facebook
                    </a>
                    <a href="{{ route('app.tiktok-ads') }}" class="text-sm px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg font-medium transition">
                        Connect TikTok
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(!$hasOpenAI)
    <div class="bg-purple-50 border border-purple-200 text-purple-800 px-6 py-4 rounded-lg mb-6">
        <div class="flex items-start gap-3">
            <span class="material-icons">auto_awesome</span>
            <div>
                <p class="font-semibold mb-2">AI Features Not Configured</p>
                <p class="text-sm mb-3">You need to configure your OpenAI API key to use AI-powered ad copy generation.</p>
                <a href="{{ route('app.ai-settings') }}#openai-connect" class="text-sm px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition inline-block">
                    Configure OpenAI
                </a>
            </div>
        </div>
    </div>
    @endif

    <form id="campaignForm" action="{{ route('app.campaign-creator.create') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Step 1: Product Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                <h2 class="text-xl font-bold text-gray-900">Product Information</h2>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product/Service Name *</label>
                    <input type="text" id="product_name" name="product_name_input" placeholder="e.g., Premium Wireless Headphones" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Description *</label>
                    <textarea id="product_description" name="product_description_input" rows="4" placeholder="Describe your product or service, its key features, and benefits..." required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience (Optional)</label>
                    <input type="text" id="target_audience" name="target_audience_input" placeholder="e.g., Tech-savvy millennials aged 25-40" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900">
                </div>
            </div>
        </div>

        <!-- Step 2: Campaign Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold">2</div>
                <h2 class="text-xl font-bold text-gray-900">Campaign Settings</h2>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Campaign Name *</label>
                    <input type="text" name="campaign_name" placeholder="My Awesome Campaign" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Campaign Objective *</label>
                    <select id="campaign_objective" name="objective" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900">
                        <option value="">Select objective...</option>
                        <option value="AWARENESS">Brand Awareness</option>
                        <option value="CONSIDERATION">Traffic & Engagement</option>
                        <option value="CONVERSION">Conversions & Sales</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Daily Budget (USD) *</label>
                    <input type="number" name="daily_budget" min="1" step="0.01" placeholder="50.00" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tone for AI Copy *</label>
                    <select id="tone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900">
                        <option value="">Select tone...</option>
                        <option value="professional">Professional</option>
                        <option value="casual">Casual</option>
                        <option value="exciting">Exciting</option>
                        <option value="urgent">Urgent</option>
                        <option value="friendly">Friendly</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Step 3: AI-Powered Ad Copy -->
        <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl shadow-sm border-2 border-purple-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold">3</div>
                <h2 class="text-xl font-bold text-gray-900">AI-Powered Ad Copy</h2>
                <span class="ml-auto px-3 py-1 bg-purple-600 text-white text-xs font-semibold rounded-full">AI ASSISTANT</span>
            </div>

            <div class="space-y-4">
                <!-- Headline -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Headline</label>
                        <button type="button" onclick="generateContent('headline')" class="text-sm px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition inline-flex items-center gap-1">
                            <span class="material-icons text-sm">auto_awesome</span>
                            Generate with AI
                        </button>
                    </div>
                    <textarea name="headline" id="headline_input" rows="2" placeholder="Eye-catching headline that grabs attention..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Best practice: 40 characters or less</p>
                </div>

                <!-- Primary Text -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Primary Ad Text *</label>
                        <button type="button" onclick="generateContent('primary_text')" class="text-sm px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition inline-flex items-center gap-1">
                            <span class="material-icons text-sm">auto_awesome</span>
                            Generate with AI
                        </button>
                    </div>
                    <textarea name="primary_text" id="primary_text_input" rows="5" placeholder="The main text of your ad. Tell your story and connect with your audience..." required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Best practice: 125 words or less</p>
                </div>

                <!-- Description -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <button type="button" onclick="generateContent('description')" class="text-sm px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition inline-flex items-center gap-1">
                            <span class="material-icons text-sm">auto_awesome</span>
                            Generate with AI
                        </button>
                    </div>
                    <textarea name="description" id="description_input" rows="3" placeholder="Additional description or supporting information..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Best practice: 30 words or less</p>
                </div>

                <!-- Call to Action -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Call to Action</label>
                        <button type="button" onclick="generateContent('cta')" class="text-sm px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition inline-flex items-center gap-1">
                            <span class="material-icons text-sm">auto_awesome</span>
                            Generate with AI
                        </button>
                    </div>
                    <input type="text" name="call_to_action" id="cta_input" placeholder="Shop Now" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900">
                    <p class="text-xs text-gray-500 mt-1">Example: "Shop Now", "Learn More", "Sign Up"</p>
                </div>
            </div>
        </div>

        <!-- Step 4: Upload Media -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold">4</div>
                <h2 class="text-xl font-bold text-gray-900">Upload Media *</h2>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-4">
                    Upload images or videos for your ad creatives. You can upload multiple files (up to 10).
                </p>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-2">
                        <span class="material-icons text-blue-600 text-sm mt-0.5">info</span>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Requirements:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li><strong>Images:</strong> JPG, PNG (recommended: 1200x628px, max 30MB)</li>
                                <li><strong>Videos:</strong> MP4, MOV (recommended: 1080x1920px for TikTok, max 100MB)</li>
                                <li><strong>Facebook:</strong> Images or videos work best</li>
                                <li><strong>TikTok:</strong> Vertical videos (9:16) perform best</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-purple-500 transition" id="dropZone">
                    <input type="file" name="media_files[]" id="media_files" multiple accept="image/jpeg,image/jpg,image/png,video/mp4,video/quicktime" required class="hidden" onchange="handleFileSelect(event)">
                    <label for="media_files" class="cursor-pointer">
                        <div class="flex flex-col items-center">
                            <span class="material-icons text-6xl text-gray-400 mb-4">cloud_upload</span>
                            <p class="text-lg font-semibold text-gray-700 mb-2">Drop files here or click to upload</p>
                            <p class="text-sm text-gray-500">Support: JPG, PNG, MP4, MOV (Max 100MB per file)</p>
                        </div>
                    </label>
                </div>

                <div id="filePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
            </div>
        </div>

        <!-- Step 5: Website & Platform Selection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold">5</div>
                <h2 class="text-xl font-bold text-gray-900">Website & Platform Selection</h2>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Website URL (Optional)</label>
                <input type="url" name="website_url" placeholder="https://yourwebsite.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900">
                <p class="text-xs text-gray-500 mt-1">Where users will be directed when they click your ad</p>
            </div>

            <p class="block text-sm font-medium text-gray-700 mb-3">Select Platform(s) *</p>

            <div class="grid md:grid-cols-2 gap-4">
                @if($facebookAccounts->isNotEmpty())
                <label class="relative flex items-start p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                    <input type="checkbox" name="platforms[]" value="facebook" onchange="toggleAccountSelect('facebook')" class="mt-1">
                    <div class="ml-3 flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span class="font-semibold text-gray-900">Facebook Ads</span>
                        </div>
                        <select name="facebook_account_id" id="facebook_account_select" disabled class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900">
                            <option value="">Select account...</option>
                            @foreach($facebookAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->ad_account_name ?? $account->ad_account_id }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>
                @else
                <div class="p-4 border-2 border-gray-200 rounded-lg bg-gray-50">
                    <div class="flex items-center gap-2 mb-2 opacity-50">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span class="font-semibold text-gray-600">Facebook Ads</span>
                    </div>
                    <p class="text-sm text-gray-500">Not connected</p>
                    <a href="{{ route('app.facebook-ads') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Connect account →</a>
                </div>
                @endif

                @if($tiktokAccounts->isNotEmpty())
                <label class="relative flex items-start p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-700 transition">
                    <input type="checkbox" name="platforms[]" value="tiktok" onchange="toggleAccountSelect('tiktok')" class="mt-1">
                    <div class="ml-3 flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-6 h-6 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.53.02C1.84-.117.02 1.79.02 11.82V23.8h11.96V.02h.55zm5.66 0c-.28 0-.53.02-.79.07v12.03H23.98v-.28c0-9.65-1.54-11.65-5.79-11.82zM12.53 23.98V24h11.45v-3.08H12.53v3.06z"/>
                            </svg>
                            <span class="font-semibold text-gray-900">TikTok Ads</span>
                        </div>
                        <select name="tiktok_account_id" id="tiktok_account_select" disabled class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900">
                            <option value="">Select account...</option>
                            @foreach($tiktokAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->advertiser_name ?? $account->advertiser_id }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>
                @else
                <div class="p-4 border-2 border-gray-200 rounded-lg bg-gray-50">
                    <div class="flex items-center gap-2 mb-2 opacity-50">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.53.02C1.84-.117.02 1.79.02 11.82V23.8h11.96V.02h.55zm5.66 0c-.28 0-.53.02-.79.07v12.03H23.98v-.28c0-9.65-1.54-11.65-5.79-11.82zM12.53 23.98V24h11.45v-3.08H12.53v3.06z"/>
                        </svg>
                        <span class="font-semibold text-gray-600">TikTok Ads</span>
                    </div>
                    <p class="text-sm text-gray-500">Not connected</p>
                    <a href="{{ route('app.tiktok-ads') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Connect account →</a>
                </div>
                @endif
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('app.ad-campaigns') }}" class="px-8 py-4 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                Cancel
            </a>
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg font-bold transition inline-flex items-center gap-2">
                <span class="material-icons">rocket_launch</span>
                Create Campaign
            </button>
        </div>
    </form>
</div>

<script>
let selectedFiles = [];

function handleFileSelect(event) {
    const files = Array.from(event.target.files);
    selectedFiles = files;
    displayFilePreview();
}

function displayFilePreview() {
    const preview = document.getElementById('filePreview');
    preview.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'relative border border-gray-300 rounded-lg p-2 group';
        
        const isVideo = file.type.startsWith('video');
        const url = URL.createObjectURL(file);
        
        let mediaHTML = '';
        if (isVideo) {
            mediaHTML = `
                <video class="w-full h-32 object-cover rounded mb-2">
                    <source src="${url}" type="${file.type}">
                </video>
            `;
        } else {
            mediaHTML = `<img src="${url}" class="w-full h-32 object-cover rounded mb-2">`;
        }
        
        fileDiv.innerHTML = `
            ${mediaHTML}
            <p class="text-xs text-gray-600 truncate">${file.name}</p>
            <p class="text-xs text-gray-400">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
            <button type="button" onclick="removeFile(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                <span class="material-icons text-sm">close</span>
            </button>
        `;
        
        preview.appendChild(fileDiv);
    });
}

function removeFile(index) {
    const dataTransfer = new DataTransfer();
    const files = selectedFiles.filter((_, i) => i !== index);
    files.forEach(file => dataTransfer.items.add(file));
    document.getElementById('media_files').files = dataTransfer.files;
    selectedFiles = files;
    displayFilePreview();
}

// Drag and drop functionality
const dropZone = document.getElementById('dropZone');

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-purple-500', 'bg-purple-50');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-purple-500', 'bg-purple-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-purple-500', 'bg-purple-50');
    
    const files = Array.from(e.dataTransfer.files);
    const dataTransfer = new DataTransfer();
    files.forEach(file => dataTransfer.items.add(file));
    document.getElementById('media_files').files = dataTransfer.files;
    selectedFiles = files;
    displayFilePreview();
});

function toggleAccountSelect(platform) {
    const checkbox = event.target;
    const select = document.getElementById(platform + '_account_select');
    
    if (checkbox.checked) {
        select.disabled = false;
        select.required = true;
    } else {
        select.disabled = true;
        select.required = false;
        select.value = '';
    }
}

async function generateContent(contentType) {
    const productName = document.getElementById('product_name').value;
    const productDescription = document.getElementById('product_description').value;
    const targetAudience = document.getElementById('target_audience').value;
    const campaignObjective = document.getElementById('campaign_objective').value;
    const tone = document.getElementById('tone').value;
    
    if (!productName || !productDescription || !campaignObjective || !tone) {
        alert('Please fill in Product Name, Description, Campaign Objective, and Tone first!');
        return;
    }
    
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="material-icons text-sm animate-spin">autorenew</span> Generating...';
    
    try {
        const response = await fetch('{{ route("app.campaign-creator.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_name: productName,
                product_description: productDescription,
                target_audience: targetAudience,
                campaign_objective: campaignObjective,
                tone: tone,
                content_type: contentType
            })
        });
        
        const data = await response.json();
        
        if (response.status === 400 && data.redirect_url) {
            // API key not configured - redirect to settings
            if (confirm(data.error + '\n\nWould you like to go to AI Settings now?')) {
                window.location.href = data.redirect_url;
            }
        } else if (data.error) {
            alert(data.error);
        } else {
            document.getElementById(contentType + '_input').value = data.generated_text;
        }
    } catch (error) {
        alert('Failed to generate content. Please try again.');
        console.error(error);
    } finally {
        button.disabled = false;
        button.innerHTML = originalHtml;
    }
}
</script>
@endsection
