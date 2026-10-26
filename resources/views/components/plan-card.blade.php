@props([
    'name',
    'price',
    'period' => null,
    'subtitle',
    'popular' => false
])

<div class="border text-center flex flex-col relative transition-all duration-300 rounded-[10px] p-[28px_20px] {{ $popular ? 'bg-agau-blue-deep border-agau-blue scale-[1.04] hover:scale-[1.04] hover:-translate-y-[6px]' : 'bg-white border-agau-border hover:shadow-[0_16px_40px_rgba(15,74,122,0.12)] hover:-translate-y-[6px]' }}">
    
    @if($popular)
        <div class="absolute -top-[12px] left-1/2 -translate-x-1/2 bg-agau-blue text-white text-[0.58rem] font-bold tracking-[2px] uppercase px-[14px] py-[4px] rounded-[20px] whitespace-nowrap">Le plus populaire</div>
    @endif

    <h3 class="text-[0.7rem] font-bold tracking-[2px] uppercase mb-[12px] m-0 {{ $popular ? 'text-[#5BC4F5]' : 'text-agau-blue' }}">{{ $name }}</h3>
    
    <p class="text-[1.8rem] font-black leading-none mb-[4px] {{ $popular ? 'text-white' : 'text-agau-text' }}">
        {{ $price }} <span class="text-[0.8rem] font-semibold align-super">FCFA</span>
    </p>
    
    <p class="text-[0.8rem] mb-[16px] italic font-serif leading-[1.6] flex-1 {{ $popular ? 'text-white/75' : 'text-agau-muted' }}">{{ $subtitle }}</p>
    
    <div class="h-[1px] mb-[16px] {{ $popular ? 'bg-white/10' : 'bg-agau-border' }}"></div>
    
    <button class="w-full p-[11px] text-[0.68rem] font-bold tracking-[1.5px] uppercase cursor-pointer transition-all duration-200 font-sans rounded-[5px] mt-auto {{ $popular ? 'bg-agau-blue-mid border-[1.5px] border-agau-blue-mid text-white hover:bg-[#1B6CA8]' : 'bg-transparent border-[1.5px] border-agau-blue text-agau-blue hover:bg-agau-blue hover:text-white' }}">
        S'abonner
    </button>
</div>
