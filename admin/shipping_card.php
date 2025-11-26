<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm page-break relative label-card group hover:border-blue-300 transition-colors" data-id="<?php echo $row['id']; ?>">
    <!-- Checkbox -->
    <div class="absolute top-4 left-4 no-print z-10">
        <input type="checkbox" value="<?php echo $row['id']; ?>" class="item-checkbox w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
    </div>

    <!-- ID -->
    <div class="absolute top-4 right-4 text-slate-300 font-mono text-xs">#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></div>

    <!-- Sender -->
    <div class="text-xs text-slate-500 mb-6 border-b border-dashed border-slate-200 pb-4">
        <span class="font-bold uppercase text-slate-400 block mb-1">ผู้ส่ง (Sender)</span>
        <p class="font-bold text-slate-700">Phichai Run 2026 (ทีมงานจัดส่ง)</p>
        <p>สมาคมศิษย์เก่าโรงเรียนพิชัย 9/9 หมู่ 3 ต.ในเมือง อ.พิชัย จ.อุตรดิตถ์ 53120</p>
        <p>โทร. 055-421-406</p>
    </div>

    <!-- Receiver -->
    <div class="pl-8 mb-6 relative">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-slate-100 rounded-full"></div>
        <span class="font-bold uppercase text-slate-400 block mb-2 text-xs">ผู้รับ (Receiver)</span>
        <h3 class="font-bold text-xl text-slate-800 mb-1"><?php echo $row['full_name']; ?></h3>
        <?php 
            $formatted_address = $row['address'];
            // Format: Address ต.Subdistrict District / Province Zip
            // To: Address \n ต.Subdistrict อ.District \n จ.Province Zip
            $formatted_address = preg_replace(
                '/^(.*?)\s+ต\.(.*?)\s+(.*?)\s+\/\s+(.*?)\s+(\d{5})$/u', 
                "$1\nต.$2 อ.$3\nจ.$4 $5", 
                $formatted_address
            );
            // Fallback cleanup
            $formatted_address = str_replace(' / ', "\n", $formatted_address);

            // --- NEW: create a compact, single-line (comma-separated) version for short display ---
            // collapse any newlines into ', ', normalize spaces and remove duplicate commas/spaces
            $compact_address = preg_replace('/\s*(?:\r\n|\r|\n)\s*/', ' ', $formatted_address);
            $compact_address = preg_replace('/\s+/', ' ', $compact_address);
            $compact_address = preg_replace('/,{2,}/', ',', $compact_address);
            $compact_address = trim($compact_address, " ,");
        ?>
        <p class="text-slate-600 text-md leading-relaxed overflow-hidden" style="-webkit-line-clamp:2; display:-webkit-box; -webkit-box-orient:vertical;" title="<?php echo htmlspecialchars($formatted_address); ?>"><?php echo htmlspecialchars($compact_address); ?></p>
        <p class="text-slate-600 mt-2 font-bold"><i class="fas fa-phone-alt text-sm mr-2"></i> <?php echo $row['phone']; ?></p>
    </div>

    <!-- Order Info (Small footer) -->
    <div class="bg-slate-50 -mx-6 -mb-6 p-4 mt-4 flex justify-between items-center border-t border-slate-100">
        <div>
            <span class="block text-[10px] text-slate-400 uppercase font-bold">ประเภท</span>
            <span class="text-xs font-medium text-slate-600 truncate max-w-[150px] block" title="<?php echo $row['category']; ?>"><?php echo $row['category']; ?></span>
        </div>
        <div class="text-right">
            <span class="block text-[10px] text-slate-400 uppercase font-bold">ไซส์เสื้อ</span>
            <span class="inline-block bg-white border border-slate-200 px-2 py-0.5 rounded text-slate-800 font-bold text-sm shadow-sm"><?php echo $row['shirt_size']; ?></span>
        </div>
    </div>
</div>