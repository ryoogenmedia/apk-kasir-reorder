<div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
    <button 
        type="button"
        wire:click="decrementStock('{{ $getRecord()->id }}')"
        style="
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.35);
            transition: all 0.15s ease;
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
        "
        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(239,68,68,0.5)'"
        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(239,68,68,0.35)'"
    >
        −
    </button>
    
    <input 
        type="number" 
        inputmode="numeric"
        wire:change="updateStock('{{ $getRecord()->id }}', $event.target.value)"
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
            background: #f8fafc;
            outline: none;
            transition: all 0.15s ease;
            -moz-appearance: textfield;
        "
        onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.15)'; this.style.background='#ffffff'"
        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc'"
    >
    
    <button 
        type="button"
        wire:click="incrementStock('{{ $getRecord()->id }}')"
        style="
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.35);
            transition: all 0.15s ease;
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
        "
        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(16,185,129,0.5)'"
        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(16,185,129,0.35)'"
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
