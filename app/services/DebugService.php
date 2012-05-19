<?php


class DebugService extends CoreService {
    
    public function tweet() {
        $tweet = new Twitter(
            'sR91GO6zZQrtLAu8URFDKA',
            'k895WeTA8QgkLkyZFqw3CmQsdAfDbztj08lLMbRI7E',
            '25677741-Ywmvs6y1ihhAO4jUsyo2Ve7gU1jeQmaclX1sb0QVS',
            'WOGmAAkJHOUt4oulitA30CGxnQtCi0QOeVBUiWIxdC4'
        );
        echo $tweet->getTweet();
    }
    
    public function amazon($keyword) {
        $amazon = new Amazon('02GA6TH96EC9SQKGEJ82',
                             'IMwa34Od62qshOfUX4uFI/yqWSNkzKQLxNfPpoiz',
                             'stayazpr-22');
        $amazon->search($keyword);
    }
}
