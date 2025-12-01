<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm page-break relative label-card group hover:shadow-lg hover:border-blue-300 transition-all duration-300" data-id="<?php echo $row['id']; ?>" data-type="registration">
    <!-- Checkbox -->
    <div class="absolute top-4 left-4 no-print z-10">
        <input type="checkbox" value="<?php echo $row['id']; ?>" class="item-checkbox w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer transition-transform hover:scale-110">
    </div>

    <!-- ID -->
    <div class="absolute top-4 right-4 text-slate-300 font-mono text-xs font-bold tracking-widest">#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></div>

    <!-- Sender -->
    <div class="text-xs text-slate-500 mb-6 border-b border-dashed border-slate-200 pb-4 relative">
        <div class="absolute -left-2 top-0 w-1 h-full bg-slate-100 rounded-r-full"></div>
        <span class="font-bold uppercase text-slate-400 block mb-1 flex items-center gap-1"><i class="fas fa-paper-plane text-[10px]"></i> ผู้ส่ง (Sender)</span>
        <p class="font-bold text-slate-700 text-sm">Phichai Run 2026 (ทีมงานจัดส่ง)</p>
        <p class="mt-1">สมาคมศิษย์เก่าโรงเรียนพิชัย 9/9 หมู่ 3 ต.ในเมือง อ.พิชัย จ.อุตรดิตถ์ 53120</p>
        <p class="mt-1"><i class="fas fa-phone-alt text-[10px] mr-1"></i> 055-421-406</p>
    </div>

    <!-- Receiver -->
    <div class="pl-4 mb-6 relative">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-400 to-indigo-400 rounded-full"></div>
        <span class="font-bold uppercase text-slate-400 block mb-2 text-xs flex items-center gap-1"><i class="fas fa-map-marker-alt text-[10px]"></i> ผู้รับ (Receiver)</span>
        <h3 class="font-bold text-xl text-slate-800 mb-2"><?php echo $row['full_name']; ?></h3>
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
        <p class="text-slate-600 text-md leading-relaxed overflow-hidden font-medium" style="-webkit-line-clamp:3; display:-webkit-box; -webkit-box-orient:vertical;" title="<?php echo htmlspecialchars($formatted_address); ?>"><?php echo htmlspecialchars($compact_address); ?></p>
        <p class="text-slate-600 mt-3 font-bold flex items-center gap-2 bg-slate-50 inline-block px-2 py-1 rounded-lg border border-slate-100"><i class="fas fa-mobile-alt text-blue-500"></i> <?php echo $row['phone']; ?></p>
    </div>

    <!-- Order Info (Small footer) -->
    <div class="bg-slate-50 -mx-6 -mb-6 p-4 mt-4 flex justify-between items-center border-t border-slate-100 rounded-b-2xl">
        <div>
            <span class="block text-[10px] text-slate-400 uppercase font-bold mb-0.5">ประเภท</span>
            <span class="text-xs font-bold text-slate-600 truncate max-w-[150px] block bg-white px-2 py-1 rounded border border-slate-200 shadow-sm" title="<?php echo $row['category']; ?>"><?php echo $row['category']; ?></span>
        </div>
        <div class="text-right">
            <span class="block text-[10px] text-slate-400 uppercase font-bold mb-0.5">ไซส์เสื้อ</span>
            <span class="inline-block bg-indigo-50 border border-indigo-100 px-2 py-1 rounded text-indigo-700 font-bold text-sm shadow-sm min-w-[40px] text-center"><?php echo $row['shirt_size']; ?></span>
        </div>
    </div>
</div>