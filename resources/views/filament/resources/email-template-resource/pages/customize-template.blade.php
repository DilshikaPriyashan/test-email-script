<x-filament-panels::page>
    {{-- TODO: cusretmize this screen to blend with this view --}}
    <div class="flex justify-between">
        <div>
            <div>Availabel attributes</div>
            @foreach ($customAttributes as $customAttribute)
                <span 
                class="inline-flex cursor-pointer items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                onclick='navigator.clipboard.writeText("{"+"{{$customAttribute}}"+"}")'
                >{{$customAttribute}}</span>
            @endforeach
        </div>
        <x-filament::button type="submit" size="sm" onclick="saveData()">
            Save Design
        </x-filament::button>
    </div>
    <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
    <script src="https://unpkg.com/grapesjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/grapesjs-preset-newsletter@1.0.2/dist/index.js"></script>
    <div class="h-screen">
        <div id="gjs" style="overflow:hidden" wire:ignore>
            {!! $htmlBody !!}
        </div>
    </div>

    <script type="text/javascript">
        window.onload = () => {
            window.editor = grapesjs.init({
                height: '100%',
                storageManager: false,
                container: '#gjs',
                fromElement: true,
                plugins: ['grapesjs-preset-newsletter'],
                pluginsOpts: {
                    'grapesjs-preset-newsletter': {
                        modalLabelImport: 'Paste all your code here below and click import',
                        modalLabelExport: 'Copy the code and use it wherever you want',
                        importPlaceholder: '<table class="table"><tr><td class="cell">Hello world!</td></tr></table>',
                        cellStyle: {
                            'font-size': '12px',
                            'font-weight': 300,
                            'vertical-align': 'top',
                            color: 'rgb(111, 119, 125)',
                            margin: 0,
                            padding: 0,
                        }
                    }
                }
            });            
        };

        function saveData(){
            Livewire.dispatch('save-template', {html: window.editor.runCommand('gjs-get-inlined-html')});
        }

    </script>
</x-filament-panels::page>
