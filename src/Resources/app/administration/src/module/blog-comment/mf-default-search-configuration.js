/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import { searchRankingPoint } from 'src/app/service/search-ranking.service';

const mfDefaultSearchConfiguration = {
    _searchable: true,
    text: {
        _searchable: true,
        _score: searchRankingPoint.HIGH_SEARCH_RANKING,
    },
};

export default mfDefaultSearchConfiguration;