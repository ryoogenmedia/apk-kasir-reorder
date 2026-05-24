@php
    $canEdit = auth()->user() && auth()->user()->hasAnyRole(['owner', 'admin']);
@endphp
<div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
    <button 
        type="button"
        @if($canEdit)
            wire:click="decrementStock('{{ $getRecord()->id }}')"
            onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(239,68,68,0.5)'"
            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(239,68,68,0.35)'"
        @else
            disabled
        @endif
        style="
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: {{ $canEdit ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : '#cbd5e1' }};
            color: #ffffff;
            border: none;
            cursor: {{ $canEdit ? 'pointer' : 'not-allowed' }};
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: {{ $canEdit ? '0 2px 6px rgba(239, 68, 68, 0.35)' : 'none' }};
            transition: all 0.15s ease;
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
            opacity: {{ $canEdit ? '1' : '0.6' }};
        "
    >
        −
    </button>
    
    <input 
        type="number" 
        inputmode="numeric"
        @if($canEdit)
            wire:change="updateStock('{{ $getRecord()->id }}', $event.target.value)"
            onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.15)'; this.style.background='#ffffff'"
            onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc'"
        @else
            disabled
        @endif
        value="{{ $getState() }}"
        min="0"
        style="
            width: 80px;
            height: 38px;
            text-align: center;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            background: {{ $canEdit ? '#f8fafc' : '#f1f5f9' }};
            outline: none;
            transition: all 0.15s ease;
            -moz-appearance: textfield;
            cursor: {{ $canEdit ? 'text' : 'not-allowed' }};
        "
    >
    
    <button 
        type="button"
        @if($canEdit)
            wire:click="incrementStock('{{ $getRecord()->id }}')"
            onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(16,185,129,0.5)'"
            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(16,185,129,0.35)'"
        @else
            disabled
        @endif
        style="
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: {{ $canEdit ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : '#cbd5e1' }};
            color: #ffffff;
            border: none;
            cursor: {{ $canEdit ? 'pointer' : 'not-allowed' }};
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: {{ $canEdit ? '0 2px 6px rgba(16, 185, 129, 0.35)' : 'none' }};
            transition: all 0.15s ease;
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
            opacity: {{ $canEdit ? '1' : '0.6' }};
        "
    >
        +
    </button>
</div>

<style>
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
</style>
