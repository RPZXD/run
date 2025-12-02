<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm page-break relative label-card group hover:shadow-lg hover:border-blue-300 transition-all duration-300" data-id="<?php echo $row['id']; ?>" data-type="registration">
    <!-- Checkbox (hidden in print) -->
    <div class="absolute top-4 left-4 no-print z-10">
        <input type="checkbox" value="<?php echo $row['id']; ?>" class="item-checkbox w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer transition-transform hover:scale-110">
    </div>

    <!-- Type Badge (for print) -->
    <div class="type-badge reg">สมัครวิ่ง</div>

    <!-- ID -->
    <div class="order-id absolute top-4 right-4 text-slate-300 font-mono text-xs font-bold tracking-widest">#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></div>

    <!-- Sender -->
    <div class="sender-section text-xs text-slate-500 mb-4 border-b border-dashed border-slate-200 pb-3 pt-6 relative">
        <div class="absolute -left-2 top-0 w-1 h-full bg-slate-100 rounded-r-full no-print"></div>
        <span class="font-bold uppercase text-slate-400 block mb-1 flex items-center gap-1"><i class="fas fa-paper-plane text-[10px] no-print"></i> ผู้ส่ง (Sender)</span>
        <p class="font-bold text-slate-700 text-sm">Phichai Run 2026 (ทีมงานจัดส่ง)</p>
        <p class="mt-1">สมาคมศิษย์เก่าโรงเรียนพิชัย 9/9 หมู่ 3 ต.ในเมือง อ.พิชัย จ.อุตรดิตถ์ 53120</p>
        <p class="mt-1"><i class="fas fa-phone-alt text-[10px] mr-1 no-print"></i>055-421-406</p>
    </div>

    <!-- Receiver -->
    <div class="receiver-section pl-4 mb-4 relative">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-400 to-indigo-400 rounded-full"></div>
        <span class="font-bold uppercase text-slate-400 block mb-2 text-xs flex items-center gap-1"><i class="fas fa-map-marker-alt text-[10px] no-print"></i> ผู้รับ (Receiver)</span>
        <h3 class="font-bold text-xl text-slate-800 mb-2"><?php echo htmlspecialchars($row['full_name']); ?></h3>
        <?php 
            $raw_address = $row['address'];
            // Try to format address nicely for print
            // Pattern: บ้านเลขที่ ต.ตำบล อำเภอ / จังหวัด รหัส
            $formatted_address = preg_replace(
                '/^(.*?)\s+ต\.(.*?)\s+(.*?)\s+\/\s+(.*?)\s+(\d{5})$/u', 
                "$1\nต.$2 อ.$3\nจ.$4 $5", 
                $raw_address
            );
            // Fallback: just replace / with newline
            if ($formatted_address === $raw_address) {
                $formatted_address = preg_replace('/\s*\/\s*/u', "\n", $raw_address);
            }

            // Compact address for screen display (single line)
            $compact_address = preg_replace('/\s*[\r\n]+\s*/', ' ', $formatted_address);
            $compact_address = preg_replace('/\s+/', ' ', $compact_address);
            $compact_address = trim($compact_address);
        ?>
        <p class="address-text text-slate-600 text-md leading-relaxed font-medium screen-address" style="-webkit-line-clamp:3; display:-webkit-box; -webkit-box-orient:vertical; overflow:hidden;" title="<?php echo htmlspecialchars($raw_address); ?>"><?php echo htmlspecialchars($compact_address); ?></p>
        <p class="address-text print-address text-slate-600 text-md leading-relaxed font-medium"><?php echo nl2br(htmlspecialchars($formatted_address)); ?></p>
        <p class="phone-text text-slate-600 mt-3 font-bold flex items-center gap-2 bg-slate-50 inline-block px-2 py-1 rounded-lg border border-slate-100"><i class="fas fa-mobile-alt text-blue-500 no-print"></i><?php echo htmlspecialchars($row['phone']); ?></p>
    </div>

    <!-- Order Info (Footer) -->
    <div class="footer-section bg-slate-50 -mx-6 -mb-6 p-4 mt-auto flex justify-between items-center border-t border-slate-100 rounded-b-2xl">
        <div>
            <span class="block text-[10px] text-slate-400 uppercase font-bold mb-0.5">ประเภท</span>
            <span class="category-text text-xs font-bold text-slate-600 truncate max-w-[150px] block bg-white px-2 py-1 rounded border border-slate-200 shadow-sm" title="<?php echo htmlspecialchars($row['category']); ?>"><?php echo htmlspecialchars($row['category']); ?></span>
        </div>
        <div class="text-right">
            <span class="block text-[10px] text-slate-400 uppercase font-bold mb-0.5">ไซส์เสื้อ</span>
            <span class="size-text inline-block bg-indigo-50 border border-indigo-100 px-2 py-1 rounded text-indigo-700 font-bold text-sm shadow-sm min-w-[40px] text-center"><?php echo htmlspecialchars($row['shirt_size']); ?></span>
        </div>
    </div>
</div>